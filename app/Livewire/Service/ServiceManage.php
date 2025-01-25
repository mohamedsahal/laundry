<?php

namespace App\Livewire\Service;

use Livewire\Component;
use App\Http\Livewire\Admin\Service\ServiceType as ServiceServiceType;
use App\Models\Service;
use App\Models\ServiceDetail;
use File;
use App\Models\ServiceType;
use App\Models\Translation;
use Livewire\Attributes\Title;

class ServiceManage extends Component
{
    public $services, $files, $imageicon, $inputs = [], $service_types, $prices = [], $servicetypes = [], $inputi = 1, $service_name, $is_active = 1, $lang;
    /* render the page */
    #[Title('Manage Service')]
    public function render()
    {
        return view('livewire.service.service-manage');
    }
    /* process before mount */
    public function mount()
    {
        if(!\Illuminate\Support\Facades\Gate::allows('service_create')){
            abort(404);
        }
        $files = File::files(public_path('assets/img/service-icons'));
        $i = 0;
        $this->service_types = ServiceType::latest()->get();
        foreach ($files as $value) {
            $i++;
            $this->files[$i]['path'] = $value->getfilename();
        }
        if (session()->has('selected_language')) { /* if session has selected language */
            $this->lang = Translation::where('id', session()->get('selected_language'))->first();
        } else {
            $this->lang = Translation::where('default', 1)->first();
        }
    }
    /* select Icon */
    public function selectIcon($data)
    {
        $this->imageicon = $this->files[$data];
        $this->dispatch('closemodal');
    }
    /* add the content for upcoming process */
    public function add($i)
    {
        $i = $i + 1;
        $this->inputi = $i;
        array_push($this->inputs, $i);
        $this->prices[$i]    = 100;
        $this->servicetypes[$i] = '';
    }
    /* save the service */
    public function save()
    {
        $this->validate([
            'servicetypes.*' => 'required',
            'prices.*'  => 'numeric|required',
            'service_name'  => 'required',
        ]);
        /* if image icon is not selected send error alert*/
        if (!$this->imageicon) {
            $this->addError('icon', "Please select an icon");
            return 1;
        }
        /* if service is not selected */
        if (count($this->inputs) <= 0) {
            $this->addError('inputerror', "You must add atleast one service type");
            return 1;
        }
        $service = Service::create([
            'service_name'  => $this->service_name,
            'icon'  => $this->imageicon['path'],
            'is_active' => $this->is_active
        ]);
        foreach ($this->inputs as $key => $value) {
            $servicetype = ServiceType::where('id', $this->servicetypes[$value])->first();
            if ($servicetype) {
                ServiceDetail::create([
                    'service_id' => $service->id,
                    'service_type_id'    => $servicetype->id,
                    'service_price'  => $this->prices[$value],
                ]);
            }
        }
        $this->dispatch(
            'alert',
            ['type' => 'success',  'message' => 'Service has been created!']
        );
        return redirect()->route('service');
    }
    /* remove the service */
    public function remove($id, $value)
    {
        if (isset($this->inputs[$id])) {
            unset($this->inputs[$id]);
            unset($this->servicetypes[$value]);
            unset($this->prices[$value]);
        }
    }
}
