<div class="dashboard-main-body">
    <div class="card h-100 p-0 radius-12">
        <div class="tw-py-1.5 tw-px-3 bg-base d-flex align-items-center flex-wrap gap-3 justify-content-between">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <form class="navbar-search">
                    <input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="{{ $lang->data['search_here'] ?? 'Search Here' }}" wire:model.live="search">
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>
            @can('customer_create')
            <button type="button" class="btn btn-primary text-sm btn-sm radius-8 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#exampleModal" wire:click="resetInputFields()">
                <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                {{ $lang->data['add_new_customer'] ?? 'Add New Customer' }}
            </button>
            @endcan
        </div>
        <div class="card-body p-0">
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col" class="">{{ $lang->data['customer_name'] ?? 'Customer Name' }}</th>
                            <th scope="col" class="">{{ $lang->data['contact'] ?? 'Contact' }}</th>
                            <th scope="col" class="">{{ $lang->data['address'] ?? 'Address' }}</th>
                            <th scope="col" class="text-center">{{ $lang->data['action'] ?? 'Action' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($customers) > 0)
                        @foreach ($customers as $row)
                        <tr>
                            <td>{{$loop->index + 1}}</td>
                            <td class="">{{$row->name}}</td>
                            <td class="">
                                <p>{{getCountryCode()}}{{ (int)$row->phone }}</p>
                                <p>{{ $row->email }}</p>
                            </td>
                            <td class="">
                                {{$row->address}}
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                    @can('customer_edit')
                                    <a href="{{route('customers.view',$row->id)}}" type="button" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium tw-size-8 d-flex justify-content-center align-items-center rounded-circle">
                                        <iconify-icon icon="lucide:eye" class="menu-icon"></iconify-icon>
                                    </a>
                                    @endcan
                                    @can('customer_view')
                                    <a href="{{route('customers.ledger',$row->id)}}" class="bg-warning-100 text-warning-600 bg-hover-warning-200 fw-medium tw-size-8 d-flex justify-content-center align-items-center rounded-circle">
                                        <iconify-icon icon="vaadin:book-dollar" class="menu-icon"></iconify-icon>
                                    </a>
                                    @endcan
                                    @can('customer_edit')
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#editModal" wire:click="edit({{ $row->id }})" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium tw-size-8 d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#exampleModalEdit">
                                        <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                    </button>
                                    @endcan
                                    @can('customer_delete')
                                    <a href="#" wire:click="delete({{$row->id}})" type="button" class="remove-item-button bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium tw-size-8 d-flex justify-content-center align-items-center rounded-circle">
                                        <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
                @if($hasMorePages)
                <div x-data="{
                                init () {
                                    let observer = new IntersectionObserver((entries) => {
                                        entries.forEach(entry => {
                                            if (entry.isIntersecting) {
                                                @this.call('loadCustomers')
                                                console.log('loading...')
                                            }
                                        })
                                    }, {
                                        root: null
                                    });
                                    observer.observe(this.$el);
                                }
                            }" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mt-4">
                    <div class="text-center pb-2 d-flex justify-content-center align-items-center">
                    {{ $lang->data['loading'] ?? 'Loading...' }}
                        <div class="spinner-grow d-inline-flex mx-2 text-primary" role="status">
                            <span class="visually-hidden"> {{ $lang->data['loading'] ?? 'Loading...' }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @if(count($customers) == 0)
            <x-empty-item />
            @endif
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title text-md" id="exampleModalLabel">{{ $lang->data['add_new_customer'] ?? 'Add New Customer' }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <form action="#">
                        <div class="row">
                            <div class="col-6 mb-20">
                                <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">{{ $lang->data['customer_name'] ?? 'Customer Name' }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control radius-8" placeholder="{{ $lang->data['enter_customer_name'] ?? 'Enter Customer Name' }}" wire:model="name">
                                @error('name')
                                <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mb-20">
                                <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">{{ $lang->data['phone_number'] ?? 'Phone Number' }} <span class="text-danger">*</span></label>
                                <input type="number" class="form-control radius-8" placeholder="{{ $lang->data['enter_phone_number'] ?? 'Enter Phone Number' }}" wire:model="phone">
                                @error('phone')
                                <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mb-20">
                                <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">{{ $lang->data['email'] ?? 'Email' }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control radius-8" placeholder="{{ $lang->data['enter_email'] ?? 'Enter Email' }}" wire:model="email">
                                @error('email')
                                <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mb-20">
                                <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">{{ $lang->data['tax_number'] ?? 'Tax Number' }}</label>
                                <input type="text" class="form-control radius-8" placeholder="{{ $lang->data['enter_tax_number'] ?? 'Enter Tax Number' }}" wire:model="tax_number">
                            </div>
                            <div class="col-12 mb-20">
                                <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">{{ $lang->data['address'] ?? 'Address' }}</label>
                                <textarea class="form-control radius-8" placeholder="{{ $lang->data['enter_address'] ?? 'Enter Address' }}" wire:model="address"></textarea>
                            </div>
                            <div class="col-12 tw-mt-6">
                                <div class="form-switch switch-primary d-flex align-items-center gap-3">
                                    <input class="form-check-input" type="checkbox" role="switch" id="switch1" checked="" wire:model="is_active">
                                    <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="switch1">{{ $lang->data['is_active'] ?? 'Is Active' }} ?</label>
                                </div>
                            </div>
                            <div class="d-flex align-items-start justify-content-end gap-3 mt-24">
                                <button type="reset" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-40 py-11 radius-8" wire:click.prevent="$dispatch('closemodal')">
                                    {{ $lang->data['cancel'] ?? 'Cancel' }}
                                </button>
                                <button type="submit" class="btn btn-primary border border-primary-600 text-md px-24 py-12 radius-8" wire:click.prevent="store">
                                    {{ $lang->data['save'] ?? 'Save' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered">
            <div class="modal-content radius-16 bg-base">
                <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                    <h1 class="modal-title text-md" id="exampleModalLabel">{{ $lang->data['edit_customer'] ?? 'Edit Customer' }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <form action="#">
                        <div class="row">
                            <div class="col-6 mb-20">
                                <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">{{ $lang->data['customer_name'] ?? 'Customer Name' }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control radius-8" placeholder="{{ $lang->data['enter_customer_name'] ?? 'Enter Customer Name' }}" wire:model="name">
                                @error('name')
                                <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mb-20">
                                <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">{{ $lang->data['phone_number'] ?? 'Phone Number' }} <span class="text-danger">*</span></label>
                                <input type="number" class="form-control radius-8" placeholder="{{ $lang->data['enter_phone_number'] ?? 'Enter Phone Number' }}" wire:model="phone">
                                @error('phone')
                                <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mb-20">
                                <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">{{ $lang->data['email'] ?? 'Email' }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control radius-8" placeholder="{{ $lang->data['enter_email'] ?? 'Enter Email' }}" wire:model="email">
                                @error('email')
                                <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mb-20">
                                <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">{{ $lang->data['tax_number'] ?? 'Tax Number' }}</label>
                                <input type="text" class="form-control radius-8" placeholder="{{ $lang->data['enter_tax_number'] ?? 'Enter Tax Number' }}" wire:model="tax_number">
                            </div>
                            <div class="col-12 mb-20">
                                <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">{{ $lang->data['address'] ?? 'Address' }}</label>
                                <textarea class="form-control radius-8" placeholder="{{ $lang->data['enter_address'] ?? 'Enter Address' }}" wire:model="address"></textarea>
                            </div>
                            <div class="col-12 tw-mt-6">
                                <div class="form-switch switch-primary d-flex align-items-center gap-3">
                                    <input class="form-check-input" type="checkbox" role="switch" id="switch1" checked="" wire:model="is_active">
                                    <label class="form-check-label line-height-1 fw-medium text-secondary-light" for="switch1">{{ $lang->data['is_active'] ?? 'Is Active' }} ?</label>
                                </div>
                            </div>
                            <div class="d-flex align-items-start justify-content-end gap-3 mt-24">
                                <button type="reset" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-40 py-11 radius-8" wire:click.prevent="$dispatch('closemodal')">
                                    {{ $lang->data['cancel'] ?? 'Cancel' }}
                                </button>
                                <button type="submit" class="btn btn-primary border border-primary-600 text-md px-24 py-12 radius-8" wire:click.prevent="update">
                                    {{ $lang->data['save'] ?? 'Save' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>