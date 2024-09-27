@extends('master')
@section('title', 'Procurement System')

@section('info')
    <style>
        .button-group {
            display: flex;
            justify-content: center;
            gap: 1rem;
            /* ระยะห่างระหว่างปุ่ม */
        }

        .card-header {
            color: #ffffff;
            /* เปลี่ยนสีข้อความ */

        }
    </style>

    <div class="card">
        <div class="card-body">
            <h2 style="color: rgb(255, 0, 0); text-align: center;">เอกสารจัดซื้อจัดจ้าง</h2>
            <form action="{{ empty($info->id) ? url('/page') : url('/page/' . $info->id) }}" method="post">
                @if (!empty($info->id))
                    @method('put')
                @endif

                @csrf
                <input type="hidden" name="template_source" value="formk">
                <div class="card mb-4">
                    <h3 class="card-header" style="background-color: #092174;">ข้อมูล</h3>
                    <div class="card-body" style="background-color: #6db8ff;">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="methode_name" class="form-label">ประเภท</label>
                                <select class="form-control" id="methode_name" name="methode_name" required>
                                    <option value="" disabled selected>เลือกประเภท</option>
                                    <option value="จัดซื้อ"
                                        {{ old('methode_name', $info->methode_name ?? '') == 'จัดซื้อ' ? 'selected' : '' }}>
                                        จัดซื้อ</option>
                                    <option value="จัดจ้าง"
                                        {{ old('methode_name', $info->methode_name ?? '') == 'จัดจ้าง' ? 'selected' : '' }}>
                                        จัดจ้าง</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label">วันที่</label>
                                <input type="date" class="form-control" id="date" name="date" required
                                    value="{{ old('date', $info->date ? $info->date->format('Y-m-d') : '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason_description" class="form-label">เหตุผล</label>
                            <textarea class="form-control" id="reason_description" name="reason_description" rows="3"
                                placeholder="เหตุผลในการจัดซื้อจัดจ้าง" required>{{ old('reason_description', $info->reason_description ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="office_name" class="form-label">สังกัด</label>
                            <input type="text" class="form-control" id="office_name" name="office_name" placeholder="สังกัด"
                                required value="{{ old('office_name', $info->office_name ?? '') }}">
                        </div>


                        <div class="mb-3">
                            <label for="devilvery_time" class="form-label">ระยะเวลาแล้วเสร็จ</label>
                            <input type="text" class="form-control" id="devilvery_time" name="devilvery_time"
                                placeholder="ระยะเวลาแล้วเสร็จ" required
                                value="{{ old('devilvery_time', $info->devilvery_time ?? '') }}">
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // ฟังก์ชันแปลงวันที่
                        function formatDate(dateString) {
                            const months = [
                                "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
                                "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
                            ];
                            const date = new Date(dateString);
                            const day = date.getDate();
                            const month = months[date.getMonth()];
                            const year = date.getFullYear() + 543; // เพิ่ม 543 สำหรับปีพุทธศักราช
                            return `${day} ${month} ${year}`;
                        }

                        // อัปเดตวันที่เมื่อมีการเปลี่ยนแปลงฟิลด์วันที่
                        document.getElementById('date').addEventListener('change', function() {
                            const dateValue = this.value;
                            document.getElementById('date_display').value = dateValue ? formatDate(dateValue) : '';
                        });
                    });
                </script>

                <!-- Products Section -->
                <div class="card mb-4">
                    <h3 class="card-header" style="background-color: #092174;">รายละเอียด</h3>
                    <div class="card-body" style="background-color: #6db8ff;">
                        <div class="mb-3">
                            <div id="products-container">
                                @foreach ($info->products as $product)
                                    <div class="product-entry mb-3">
                                        <input type="hidden" name="products[{{ $loop->index }}][id]"
                                            value="{{ $product->id }}">
                                        <div class="mb-2">
                                            <label for="products[{{ $loop->index }}][product_type]" class="form-label">ประเภทผลิตภัณฑ์</label>
                                            <select class="form-control" id="products[{{ $loop->index }}][product_type]"
                                                name="products[{{ $loop->index }}][product_type]" required>
                                                <option value="" disabled {{ old('products.' . $loop->index . '.product_type', $product->product_type) == '' ? 'selected' : '' }}>เลือกประเภท</option>
                                                <option value="วัสดุ" {{ old('products.' . $loop->index . '.product_type', $product->product_type) == 'วัสดุ' ? 'selected' : '' }}>วัสดุ</option>
                                                <option value="ครุภัณฑ์" {{ old('products.' . $loop->index . '.product_type', $product->product_type) == 'ครุภัณฑ์' ? 'selected' : '' }}>ครุภัณฑ์</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label for="products[{{ $loop->index }}][product_name]"
                                                class="form-label">ชื่อผลิตภัณฑ์</label>
                                            <input type="text" class="form-control"
                                                id="products[{{ $loop->index }}][product_name]"
                                                name="products[{{ $loop->index }}][product_name]"
                                                placeholder="ชื่อผลิตภัณฑ์"
                                                value="{{ old('products.' . $loop->index . '.product_name', $product->product_name) }}">
                                        </div>
                                        <div class="mb-2">
                                            <label for="products[{{ $loop->index }}][quantity]"
                                                class="form-label">จำนวน</label>
                                            <input type="number" class="form-control"
                                                id="products[{{ $loop->index }}][quantity]"
                                                name="products[{{ $loop->index }}][quantity]" placeholder="จำนวน"
                                                value="{{ old('products.' . $loop->index . '.quantity', $product->quantity) }}">
                                        </div>
                                        <div class="mb-2">
                                            <label for="products[{{ $loop->index }}][unit]"
                                                class="form-label">หน่วย</label>
                                            <input type="text" class="form-control"
                                                id="products[{{ $loop->index }}][unit]"
                                                name="products[{{ $loop->index }}][unit]" placeholder="หน่วย"
                                                value="{{ old('products.' . $loop->index . '.unit', $product->unit) }}">
                                        </div>
                                        <div class="mb-2">
                                            <label for="products[{{ $loop->index }}][product_price]"
                                                class="form-label">ราคาผลิตภัณฑ์</label>
                                            <input type="text" class="form-control product-price"
                                                id="products[{{ $loop->index }}][product_price]"
                                                name="products[{{ $loop->index }}][product_price]"
                                                placeholder="ราคาผลิตภัณฑ์"
                                                value="{{ old('products.' . $loop->index . '.product_price', number_format($product->product_price, 2)) }}">
                                        </div>
                                    </div>
                                @endforeach
                                <!-- ฟอร์มสำหรับการเพิ่มผลิตภัณฑ์ -->
                                @if (!isset($info->id))
                                    <div class="product-entry mb-3 d-flex">
                                    <div class="mb-2 me-2" style="flex-basis: 150px;">
                                        <label for="products[${index}][product_type]" class="form-label">ประเภทผลิตภัณฑ์</label>
                                        <select class="form-control" id="products[${index}][product_type]"
                                            name="products[${index}][product_type]" required>
                                            <option value="" disabled selected>เลือกประเภท</option>
                                            <option value="วัสดุ">วัสดุ</option>
                                            <option value="ครุภัณฑ์">ครุภัณฑ์</option>
                                        </select>
                                    </div>
                                        <div class="flex-grow-1 mb-2 me-2">
                                            <label for="products[${index}][product_name]"
                                                class="form-label">ชื่อผลิตภัณฑ์</label>
                                            <input type="text" class="form-control"
                                                id="products[${index}][product_name]"
                                                name="products[${index}][product_name]" placeholder="ชื่อผลิตภัณฑ์"
                                                required>
                                        </div>
                                        <div class="mb-2 me-2" style="flex-basis: 150px;">
                                            <label for="products[${index}][quantity]" class="form-label">จำนวน</label>
                                            <input type="number" class="form-control" id="products[${index}][quantity]"
                                                name="products[${index}][quantity]" placeholder="จำนวน" required>
                                        </div>
                                        <div class="mb-2 me-2" style="flex-basis: 150px;">
                                            <label for="products[${index}][unit]" class="form-label">หน่วย</label>
                                            <input type="text" class="form-control" id="products[${index}][unit]"
                                                name="products[${index}][unit]" placeholder="หน่วย" required>
                                        </div>
                                        <div class="mb-2" style="flex-basis: 150px;">
                                            <label for="products[${index}][product_price]"
                                                class="form-label">ราคาผลิตภัณฑ์/หน่วย</label>
                                            <input type="text" class="form-control product-price"
                                                id="products[${index}][product_price]"
                                                name="products[${index}][product_price]" placeholder="ราคาผลิตภัณฑ์"
                                                required>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-secondary"
                                onclick="addProduct()">เพิ่มผลิตภัณฑ์</button>
                            <br></br>
                            <div class="mb-2">
                                <label class="form-label">รวมราคาทั้งหมด</label>
                                <input type="text" class="form-control" id="total-price" placeholder="รวมราคาทั้งหมด"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // ฟังก์ชันสำหรับจัดรูปแบบตัวเลขเป็นค่าเงิน
                        function formatCurrency(value) {
                            return new Intl.NumberFormat('th-TH', {
                                style: 'decimal',
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }).format(value);
                        }

                        // ฟังก์ชันสำหรับแปลงค่าจากรูปแบบที่มี , กลับไปเป็นตัวเลขปกติ
                        function unformatCurrency(value) {
                            return value.replace(/[^0-9.-]+/g, '');
                        }

                        // ฟังก์ชันคำนวณราคาผลิตภัณฑ์รวม
                        function calculateTotalPrice() {
                            let totalPrice = 0;

                            // คำนวณรวมราคาทั้งหมดจากข้อมูลผลิตภัณฑ์
                            document.querySelectorAll('.product-entry').forEach(entry => {
                                const quantity = parseFloat(entry.querySelector('input[name$="[quantity]"]').value) ||
                                    0;
                                const productPrice = parseFloat(unformatCurrency(entry.querySelector(
                                    'input[name$="[product_price]"]').value)) || 0;
                                totalPrice += quantity * productPrice;
                            });

                            // อัปเดตรวมราคาทั้งหมด
                            document.getElementById('total-price').value = formatCurrency(totalPrice);
                        }

                        // เพิ่ม event listeners ให้กับฟิลด์ที่อาจมีการเปลี่ยนแปลง
                        function addEventListeners() {
                            document.querySelectorAll('.product-price').forEach(input => {
                                input.addEventListener('input', function(e) {
                                    let value = e.target.value;
                                    // เมื่อกรอกข้อมูลในฟิลด์ product_price ให้จัดรูปแบบตัวเลข
                                    e.target.value = formatCurrency(unformatCurrency(value));
                                    calculateTotalPrice();
                                });
                            });

                            document.querySelectorAll('input[name$="[quantity]"]').forEach(input => {
                                input.addEventListener('input', calculateTotalPrice);
                            });
                        }

                        // เรียกใช้ฟังก์ชันคำนวณยอดรวมเมื่อโหลดหน้า
                        calculateTotalPrice();
                        addEventListeners();

                        window.addProduct = function() {
                            const container = document.getElementById('products-container');
                            const index = container.children.length;
                            const template = `
                            <div class="product-entry mb-3 d-flex">
                                    <div class="mb-2 me-2" style="flex-basis: 150px;">
                                        <label for="products[${index}][product_type]" class="form-label">ประเภทผลิตภัณฑ์</label>
                                        <select class="form-control" id="products[${index}][product_type]"
                                            name="products[${index}][product_type]" required>
                                            <option value="" disabled selected>เลือกประเภท</option>
                                            <option value="วัสดุ">วัสดุ</option>
                                            <option value="ครุภัณฑ์">ครุภัณฑ์</option>
                                        </select>
                                    </div>
                                        <div class="flex-grow-1 mb-2 me-2">
                                            <label for="products[${index}][product_name]"
                                                class="form-label">ชื่อผลิตภัณฑ์</label>
                                            <input type="text" class="form-control"
                                                id="products[${index}][product_name]"
                                                name="products[${index}][product_name]" placeholder="ชื่อผลิตภัณฑ์"
                                                required>
                                        </div>
                                        <div class="mb-2 me-2" style="flex-basis: 150px;">
                                            <label for="products[${index}][quantity]" class="form-label">จำนวน</label>
                                            <input type="number" class="form-control" id="products[${index}][quantity]"
                                                name="products[${index}][quantity]" placeholder="จำนวน" required>
                                        </div>
                                        <div class="mb-2 me-2" style="flex-basis: 150px;">
                                            <label for="products[${index}][unit]" class="form-label">หน่วย</label>
                                            <input type="text" class="form-control" id="products[${index}][unit]"
                                                name="products[${index}][unit]" placeholder="หน่วย" required>
                                        </div>
                                        <div class="mb-2" style="flex-basis: 150px;">
                                            <label for="products[${index}][product_price]"
                                                class="form-label">ราคาผลิตภัณฑ์/หน่วย</label>
                                            <input type="text" class="form-control product-price"
                                                id="products[${index}][product_price]"
                                                name="products[${index}][product_price]" placeholder="ราคาผลิตภัณฑ์"
                                                required>
                                        </div>
                                    </div>
            `;
                            container.insertAdjacentHTML('beforeend', template);

                            // เพิ่ม event listeners ใหม่ให้กับฟิลด์ที่เพิ่มเข้ามา
                            addEventListeners();

                            // คำนวณรวมราคาทั้งหมดใหม่
                            calculateTotalPrice();
                        }
                    });
                </script>

                <!-- Sellers Section -->
                <div class="card mb-4">
                    <h3 class="card-header" style="background-color: #092174;">ผู้ขาย</h3>
                    <div class="card-body" style="background-color: #6db8ff;">
                        <div class="mb-3">
                            <div id="sellers-container">
                                @foreach ($info->sellers as $seller)
                                    <div class="seller-entry mb-3">
                                        <input type="hidden" name="sellers[{{ $loop->index }}][id]"
                                            value="{{ $seller->id }}">

                                        <!-- Dropdown สำหรับเลือกผู้ขาย -->
                                        <div class="mb-2">
                                            <label for="sellers[{{ $loop->index }}][seller_name]"
                                                class="form-label">ชื่อผู้ขาย</label>
                                            <select class="form-select"
                                                id="sellers[{{ $loop->index }}][seller_name_select]"
                                                onchange="setSellerDetails(this, 'sellers[{{ $loop->index }}][seller_name]', 'sellers[{{ $loop->index }}][address]', 'sellers[{{ $loop->index }}][taxpayer_number]')">
                                                <option value="">-- เลือกผู้ขาย --</option>
                                                @foreach ($allSellers as $availableSeller)
                                                    <option value="{{ $availableSeller->seller_name }}"
                                                        @if ($seller->seller_name == $availableSeller->seller_name) selected @endif>
                                                        {{ $availableSeller->seller_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control mt-2"
                                                id="sellers[{{ $loop->index }}][seller_name]"
                                                name="sellers[{{ $loop->index }}][seller_name]" placeholder="ชื่อผู้ขาย"
                                                value="{{ old('sellers.' . $loop->index . '.seller_name', $seller->seller_name) }}">
                                        </div>

                                        <!-- ที่อยู่ผู้ขาย -->
                                        <div class="mb-2">
                                            <label for="sellers[{{ $loop->index }}][address]"
                                                class="form-label">ที่อยู่</label>
                                            <input type="text" class="form-control"
                                                id="sellers[{{ $loop->index }}][address]"
                                                name="sellers[{{ $loop->index }}][address]" placeholder="ที่อยู่"
                                                value="{{ old('sellers.' . $loop->index . '.address', $seller->address) }}">
                                        </div>

                                        <!-- หมายเลขผู้เสียภาษี -->
                                        <div class="mb-2">
                                            <label for="sellers[{{ $loop->index }}][taxpayer_number]"
                                                class="form-label">หมายเลขผู้เสียภาษี</label>
                                            <input type="text" class="form-control"
                                                id="sellers[{{ $loop->index }}][taxpayer_number]"
                                                name="sellers[{{ $loop->index }}][taxpayer_number]"
                                                placeholder="หมายเลขผู้เสียภาษี"
                                                value="{{ old('sellers.' . $loop->index . '.taxpayer_number', $seller->taxpayer_number) }}">
                                        </div>

                                        <!-- เอกสารอ้างอิง -->
                                        <div class="mb-2">
                                            <label for="sellers[{{ $loop->index }}][reference_documents]"
                                                class="form-label">เอกสารอ้างอิง</label>
                                            <input type="text" class="form-control"
                                                id="sellers[{{ $loop->index }}][reference_documents]"
                                                name="sellers[{{ $loop->index }}][reference_documents]"
                                                placeholder="เอกสารอ้างอิง"
                                                value="{{ old('sellers.' . $loop->index . '.reference_documents', $seller->reference_documents) }}">
                                        </div>
                                    </div>
                                @endforeach

                                <!-- ฟอร์มสำหรับการเพิ่มผู้ขายใหม่ -->
                                @if (!isset($info->id))
                                    <div class="seller-entry mb-3">
                                        <div class="mb-2">
                                            <label for="sellers[0][seller_name]" class="form-label">ชื่อผู้ขาย</label>
                                            <select class="form-select" id="sellers[0][seller_name_select]"
                                                onchange="setSellerDetails(this, 'sellers[0][seller_name]', 'sellers[0][address]', 'sellers[0][taxpayer_number]')">
                                                <option value="">-- เลือกผู้ขาย --</option>
                                                @foreach ($allSellers as $availableSeller)
                                                    <option value="{{ $availableSeller->seller_name }}">
                                                        {{ $availableSeller->seller_name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control mt-2" id="sellers[0][seller_name]"
                                                name="sellers[0][seller_name]" placeholder="ชื่อผู้ขาย" required>
                                        </div>
                                        <div class="mb-2">
                                            <label for="sellers[0][address]" class="form-label">ที่อยู่</label>
                                            <input type="text" class="form-control" id="sellers[0][address]"
                                                name="sellers[0][address]" placeholder="ที่อยู่" required>
                                        </div>
                                        <div class="mb-2">
                                            <label for="sellers[0][taxpayer_number]"
                                                class="form-label">หมายเลขผู้เสียภาษี</label>
                                            <input type="text" class="form-control" id="sellers[0][taxpayer_number]"
                                                name="sellers[0][taxpayer_number]" placeholder="หมายเลขผู้เสียภาษี"
                                                required>
                                        </div>
                                        <div class="mb-2">
                                            <label for="sellers[0][reference_documents]"
                                                class="form-label">เอกสารอ้างอิง</label>
                                            <input type="text" class="form-control"
                                                id="sellers[0][reference_documents]"
                                                name="sellers[0][reference_documents]" placeholder="เอกสารอ้างอิง"
                                                required>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    function setSellerDetails(selectElement, nameInputId, addressInputId, taxpayerInputId) {
                        var selectedValue = selectElement.value;
                        var nameInput = document.getElementById(nameInputId);
                        var addressInput = document.getElementById(addressInputId);
                        var taxpayerInput = document.getElementById(taxpayerInputId);

                        @foreach ($allSellers as $availableSeller)
                            if (selectedValue === "{{ $availableSeller->seller_name }}") {
                                nameInput.value = "{{ $availableSeller->seller_name }}";
                                addressInput.value = "{{ $availableSeller->address }}";
                                taxpayerInput.value = "{{ $availableSeller->taxpayer_number }}";
                            }
                        @endforeach
                    }
                </script>



                <!-- Committee Members Section -->
                <div class="card mb-4">
                    <h3 class="card-header" style="background-color: #092174;">ผู้ลงนาม</h3>
                    <div class="card-body" style="background-color: #6db8ff;">
                        <div class="mb-3">
                            <div id="committee-container">
                                @foreach ($info->committeemembers as $committee_member)
                                    <div class="committee-member-entry mb-3">
                                        <input type="hidden" name="committeemembers[{{ $loop->index }}][id]"
                                            value="{{ $committee_member->id }}">

                                        <!-- ชื่อและตำแหน่งในบรรทัดเดียวกัน -->
                                        <div class="d-flex align-items-center">
                                            <!-- Dropdown สำหรับเลือกชื่อสมาชิก -->
                                            <div class="flex-fill me-2">
                                                <label for="committeemembers[{{ $loop->index }}][member_name]"
                                                    class="form-label">ชื่อผู้ลงนาม</label>
                                                <select class="form-select"
                                                    id="committeemembers[{{ $loop->index }}][member_name_select]"
                                                    onchange="setNameAndPosition(this, 'committeemembers[{{ $loop->index }}][member_name]', 'committeemembers[{{ $loop->index }}][member_position]')">
                                                    <option value="">-- เลือกชื่อผู้ลงนาม --</option>
                                                    @foreach ($allCommitteeMembers as $availableMember)
                                                        <option value="{{ $availableMember->member_name }}"
                                                            @if ($committee_member->member_name == $availableMember->member_name) selected @endif>
                                                            {{ $availableMember->member_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input type="text" class="form-control mt-2"
                                                    id="committeemembers[{{ $loop->index }}][member_name]"
                                                    name="committeemembers[{{ $loop->index }}][member_name]"
                                                    placeholder="ชื่อสมาชิก"
                                                    value="{{ old('committeemembers.' . $loop->index . '.member_name', $committee_member->member_name) }}">
                                            </div>

                                            <!-- Input สำหรับกรอกตำแหน่ง -->
                                            <div class="flex-fill ms-2">
                                                <label for="committeemembers[{{ $loop->index }}][member_position]"
                                                    class="form-label">ตำแหน่ง</label>
                                                <input type="text" class="form-control"
                                                    id="committeemembers[{{ $loop->index }}][member_position]"
                                                    name="committeemembers[{{ $loop->index }}][member_position]"
                                                    placeholder="ตำแหน่ง"
                                                    value="{{ old('committeemembers.' . $loop->index . '.member_position', $committee_member->member_position) }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- ฟอร์มสำหรับการเพิ่มสมาชิกคณะกรรมการใหม่ -->
                                @if (!isset($info->id))
                                    <div class="committee-member-entry mb-3 d-flex align-items-center">
                                        <div class="flex-fill me-2">
                                            <label for="committeemembers[0][member_name]"
                                                class="form-label">ชื่อผู้ลงนาม</label>
                                            <select class="form-select" id="committeemembers[0][member_name_select]"
                                                onchange="setNameAndPosition(this, 'committeemembers[0][member_name]', 'committeemembers[0][member_position]')">
                                                <option value="">-- เลือกชื่อผู้ลงนาม --</option>
                                                @foreach ($allCommitteeMembers as $availableMember)
                                                    <option value="{{ $availableMember->member_name }}">
                                                        {{ $availableMember->member_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control mt-2"
                                                id="committeemembers[0][member_name]"
                                                name="committeemembers[0][member_name]" placeholder="ชื่อผู้ลงนาม" required>
                                        </div>
                                        <div class="flex-fill ms-2">
                                            <label for="committeemembers[0][member_position]"
                                                class="form-label">ตำแหน่ง</label>
                                            <input type="text" class="form-control"
                                                id="committeemembers[0][member_position]"
                                                name="committeemembers[0][member_position]" placeholder="ตำแหน่ง"
                                                required>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    function setNameAndPosition(selectElement, nameInputId, positionInputId) {
                        var selectedValue = selectElement.value;
                        var nameInput = document.getElementById(nameInputId);
                        var positionInput = document.getElementById(positionInputId);

                        @foreach ($allCommitteeMembers as $availableMember)
                            if (selectedValue === "{{ $availableMember->member_name }}") {
                                nameInput.value = "{{ $availableMember->member_name }}";
                                positionInput.value = "{{ $availableMember->member_position }}";
                            }
                        @endforeach
                    }
                </script>


                <!-- Bidders Section -->
                <div class="card mb-4">
                    <h3 class="card-header" style="background-color: #092174;">เจ้าหน้าที่</h3>
                    <div class="card-body" style="background-color: #6db8ff;">
                        <div class="mb-3">
                            <div id="bidders-container">
                                @foreach ($info->bidders as $bidder)
                                    <div class="bidder-entry mb-3">
                                        <input type="hidden" name="bidders[{{ $loop->index }}][id]"
                                            value="{{ $bidder->id }}">

                                        <!-- ชื่อผู้เสนอราคาใน Dropdown -->
                                        <div class="mb-2">
                                            <label for="bidders[{{ $loop->index }}][bidder_name]"
                                                class="form-label">ชื่อเจ้าหน้าที่</label>
                                            <select class="form-select"
                                                id="bidders[{{ $loop->index }}][bidder_name_select]"
                                                onchange="setBidderNameAndPosition(this, 'bidders[{{ $loop->index }}][bidder_name]', 'bidders[{{ $loop->index }}][bidder_position]')">
                                                <option value="">-- เลือกเจ้าหน้าที่ --</option>
                                                @foreach ($allBidders as $availableBidder)
                                                    <option value="{{ $availableBidder->bidder_name }}"
                                                        @if ($bidder->bidder_name == $availableBidder->bidder_name) selected @endif>
                                                        {{ $availableBidder->bidder_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control mt-2"
                                                id="bidders[{{ $loop->index }}][bidder_name]"
                                                name="bidders[{{ $loop->index }}][bidder_name]"
                                                placeholder="ชื่อผู้เสนอราคา"
                                                value="{{ old('bidders.' . $loop->index . '.bidder_name', $bidder->bidder_name) }}">
                                        </div>

                                        <!-- ตำแหน่งผู้เสนอราคา -->
                                        <div class="mb-2">
                                            <label for="bidders[{{ $loop->index }}][bidder_position]"
                                                class="form-label">ตำแหน่ง</label>
                                            <input type="text" class="form-control"
                                                id="bidders[{{ $loop->index }}][bidder_position]"
                                                name="bidders[{{ $loop->index }}][bidder_position]"
                                                placeholder="ตำแหน่ง"
                                                value="{{ old('bidders.' . $loop->index . '.bidder_position', $bidder->bidder_position) }}">
                                        </div>
                                    </div>
                                @endforeach

                                <!-- ฟอร์มสำหรับการเพิ่มผู้เสนอราคาใหม่ -->
                                @if (!isset($info->id))
                                    <div class="bidder-entry mb-3 d-flex flex-wrap">
                                        <div class="flex-fill mb-2 me-2">
                                            <label for="bidders[0][bidder_name]"
                                                class="form-label">เจ้าหน้าที่</label>
                                            <select class="form-select" id="bidders[0][bidder_name_select]"
                                                onchange="setBidderNameAndPosition(this, 'bidders[0][bidder_name]', 'bidders[0][bidder_position]')">
                                                <option value="">-- เลือกเจ้าหน้าที่ --</option>
                                                @foreach ($allBidders as $availableBidder)
                                                    <option value="{{ $availableBidder->bidder_name }}">
                                                        {{ $availableBidder->bidder_name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control mt-2" id="bidders[0][bidder_name]"
                                                name="bidders[0][bidder_name]" placeholder="ชื่อเจ้าหน้าที่" required>
                                        </div>
                                        <div class="flex-fill mb-2 me-2">
                                            <label for="bidders[0][bidder_position]" class="form-label">ตำแหน่ง</label>
                                            <input type="text" class="form-control" id="bidders[0][bidder_position]"
                                                name="bidders[0][bidder_position]" placeholder="ตำแหน่ง" required>
                                        </div>
                                    </div>
                                @endif
                                <!-- ฟอร์มสำหรับการเพิ่มผู้เสนอราคาใหม่ -->
                                @if (!isset($info->id))
                                    <div class="bidder-entry mb-3 d-flex flex-wrap">
                                        <div class="flex-fill mb-2 me-2">
                                            <label for="bidders[1][bidder_name]"
                                                class="form-label">ชื่อเจ้าหน้าที่</label>
                                            <select class="form-select" id="bidders[1][bidder_name_select]"
                                                onchange="setBidderNameAndPosition(this, 'bidders[1][bidder_name]', 'bidders[1][bidder_position]')">
                                                <option value="">-- เลือกเจ้าหน้าที่ --</option>
                                                @foreach ($allBidders as $availableBidder)
                                                    <option value="{{ $availableBidder->bidder_name }}">
                                                        {{ $availableBidder->bidder_name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control mt-2" id="bidders[1][bidder_name]"
                                                name="bidders[1][bidder_name]" placeholder="ชื่อเจ้าหน้าที่" required>
                                        </div>
                                        <div class="flex-fill mb-2 me-2">
                                            <label for="bidders[1][bidder_position]" class="form-label">ตำแหน่ง</label>
                                            <input type="text" class="form-control" id="bidders[1][bidder_position]"
                                                name="bidders[1][bidder_position]" placeholder="ตำแหน่ง" required>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    function setBidderNameAndPosition(selectElement, nameInputId, positionInputId) {
                        var selectedValue = selectElement.value;
                        var nameInput = document.getElementById(nameInputId);
                        var positionInput = document.getElementById(positionInputId);

                        @foreach ($allBidders as $availableBidder)
                            if (selectedValue === "{{ $availableBidder->bidder_name }}") {
                                nameInput.value = "{{ $availableBidder->bidder_name }}";
                                positionInput.value = "{{ $availableBidder->bidder_position }}";
                            }
                        @endforeach
                    }
                </script>


                <!-- Inspectors Section -->
                <div class="card mb-4">
                    <h3 class="card-header" style="background-color: #092174;">คณะกรรมการ</h3>
                    <div class="card-body" style="background-color: #6db8ff;">
                        <div class="mb-3">
                            <div id="inspectors-container">
                                @foreach ($info->inspectors as $inspector)
                                    <div class="inspector-entry mb-3">
                                        <input type="hidden" name="inspectors[{{ $loop->index }}][id]"
                                            value="{{ $inspector->id }}">

                                        <!-- Dropdown สำหรับเลือกชื่อผู้ตรวจสอบ -->
                                        <div class="mb-2">
                                            <label for="inspectors[{{ $loop->index }}][inspector_name]"
                                                class="form-label">ชื่อกรรมการ</label>
                                            <select class="form-select"
                                                id="inspectors[{{ $loop->index }}][inspector_name_select]"
                                                onchange="setInspectorNameAndPosition(this, 'inspectors[{{ $loop->index }}][inspector_name]', 'inspectors[{{ $loop->index }}][inspector_position]')">
                                                <option value="">-- เลือกชื่อกรรมการ --</option>
                                                @foreach ($allInspectors as $availableInspector)
                                                    <option value="{{ $availableInspector->inspector_name }}"
                                                        @if ($inspector->inspector_name == $availableInspector->inspector_name) selected @endif>
                                                        {{ $availableInspector->inspector_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control mt-2"
                                                id="inspectors[{{ $loop->index }}][inspector_name]"
                                                name="inspectors[{{ $loop->index }}][inspector_name]"
                                                placeholder="ชื่อกรรมการ"
                                                value="{{ old('inspectors.' . $loop->index . '.inspector_name', $inspector->inspector_name) }}">
                                        </div>

                                        <!-- Input สำหรับกรอกตำแหน่ง -->
                                        <div class="mb-2">
                                            <label for="inspectors[{{ $loop->index }}][inspector_position]"
                                                class="form-label">ตำแหน่ง</label>
                                            <input type="text" class="form-control"
                                                id="inspectors[{{ $loop->index }}][inspector_position]"
                                                name="inspectors[{{ $loop->index }}][inspector_position]"
                                                placeholder="ตำแหน่ง"
                                                value="{{ old('inspectors.' . $loop->index . '.inspector_position', $inspector->inspector_position) }}">
                                        </div>
                                    </div>
                                @endforeach

                                <!-- ฟอร์มสำหรับการเพิ่มผู้ตรวจสอบใหม่ -->
                                @if (!isset($info->id))
                                    <div class="inspector-entry mb-3 d-flex flex-wrap">
                                        <div class="flex-fill mb-2 me-2">
                                            <label for="inspectors[0][inspector_name]"
                                                class="form-label">ชื่อกรรมการ</label>
                                            <select class="form-select" id="inspectors[0][inspector_name_select]"
                                                onchange="setInspectorNameAndPosition(this, 'inspectors[0][inspector_name]', 'inspectors[0][inspector_position]')">
                                                <option value="">-- เลือกชื่อกรรมการ --</option>
                                                @foreach ($allInspectors as $availableInspector)
                                                    <option value="{{ $availableInspector->inspector_name }}">
                                                        {{ $availableInspector->inspector_name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control mt-2"
                                                id="inspectors[0][inspector_name]" name="inspectors[0][inspector_name]"
                                                placeholder="ชื่อกรรมการ" required>
                                        </div>
                                        <div class="flex-fill mb-2 me-2">
                                            <label for="inspectors[0][inspector_position]"
                                                class="form-label">ตำแหน่ง</label>
                                            <input type="text" class="form-control"
                                                id="inspectors[0][inspector_position]"
                                                name="inspectors[0][inspector_position]" placeholder="ตำแหน่ง" required>
                                        </div>
                                    </div>
                                @endif
                                <!-- ฟอร์มสำหรับการเพิ่มผู้ตรวจสอบใหม่ -->
                                @if (!isset($info->id))
                                    <div class="inspector-entry mb-3 d-flex flex-wrap">
                                        <div class="flex-fill mb-2 me-2">
                                            <label for="inspectors[1][inspector_name]"
                                                class="form-label">ชื่อกรรมการ</label>
                                            <select class="form-select" id="inspectors[1][inspector_name_select]"
                                                onchange="setInspectorNameAndPosition(this, 'inspectors[1][inspector_name]', 'inspectors[1][inspector_position]')">
                                                <option value="">-- เลือกกรรมการ --</option>
                                                @foreach ($allInspectors as $availableInspector)
                                                    <option value="{{ $availableInspector->inspector_name }}">
                                                        {{ $availableInspector->inspector_name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control mt-2"
                                                id="inspectors[1][inspector_name]" name="inspectors[1][inspector_name]"
                                                placeholder="ชื่อกรรมการ" required>
                                        </div>
                                        <div class="flex-fill mb-2 me-2">
                                            <label for="inspectors[1][inspector_position]"
                                                class="form-label">ตำแหน่ง</label>
                                            <input type="text" class="form-control"
                                                id="inspectors[1][inspector_position]"
                                                name="inspectors[1][inspector_position]" placeholder="ตำแหน่ง" required>
                                        </div>
                                    </div>
                                @endif
                                <!-- ฟอร์มสำหรับการเพิ่มผู้ตรวจสอบใหม่ -->
                                @if (!isset($info->id))
                                    <div class="inspector-entry mb-3 d-flex flex-wrap">
                                        <div class="flex-fill mb-2 me-2">
                                            <label for="inspectors[2][inspector_name]"
                                                class="form-label">ชื่อกรรมการ</label>
                                            <select class="form-select" id="inspectors[2][inspector_name_select]"
                                                onchange="setInspectorNameAndPosition(this, 'inspectors[2][inspector_name]', 'inspectors[2][inspector_position]')">
                                                <option value="">-- เลือกชื่อกรรมการ --</option>
                                                @foreach ($allInspectors as $availableInspector)
                                                    <option value="{{ $availableInspector->inspector_name }}">
                                                        {{ $availableInspector->inspector_name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control mt-2"
                                                id="inspectors[2][inspector_name]" name="inspectors[2][inspector_name]"
                                                placeholder="ชื่อกรรมการ" required>
                                        </div>
                                        <div class="flex-fill mb-2 me-2">
                                            <label for="inspectors[2][inspector_position]"
                                                class="form-label">ตำแหน่ง</label>
                                            <input type="text" class="form-control"
                                                id="inspectors[2][inspector_position]"
                                                name="inspectors[2][inspector_position]" placeholder="ตำแหน่ง" required>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    function setInspectorNameAndPosition(selectElement, nameInputId, positionInputId) {
                        var selectedValue = selectElement.value;
                        var nameInput = document.getElementById(nameInputId);
                        var positionInput = document.getElementById(positionInputId);

                        @foreach ($allInspectors as $availableInspector)
                            if (selectedValue === "{{ $availableInspector->inspector_name }}") {
                                nameInput.value = "{{ $availableInspector->inspector_name }}";
                                positionInput.value = "{{ $availableInspector->inspector_position }}";
                            }
                        @endforeach
                    }
                </script>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">{{ empty($info->id) ? 'บันทึก' : 'อัพเดต' }}</button>
                    <a href="{{ url('/page') }}" class="btn btn-danger">ยกเลิก</a>
                </div>
            </form>

        </div>
    </div>
    </div>
@endsection
