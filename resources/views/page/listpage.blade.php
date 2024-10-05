@extends('master')
@section('title', 'Procurement System')

@section('info')
<div class="card">
    <div class="card-body">
        <h1>รายการจัดซื้อจัดจ้าง</h1>

        <!-- Search Form -->
        <form action="{{ url('/page/list') }}" method="GET" class="mb-3 d-flex justify-content-end">
            <div class="input-group" style="width: 300px;">
                <input type="text" name="search" class="form-control" placeholder="ค้นหา..."
                    value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> ค้นหา
                </button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>ประเภท</th>
                        <th>เหตุผล</th>
                        <th>ไฟล์ PDF</th>
                        <th>วันที่สร้างไฟล์</th>
                        <th>ระยะเวลาแล้วเสร็จ</th>
                        <th>การดำเนินการ</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($info as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->methode_name }}</td>
                        <td>{{ $item->reason_description }}</td>
                        <!-- เปลี่ยนจากการแสดงคณะเป็นลิงก์ชื่อไฟล์ PDF และแสดงเป็น popup -->
                        <td>
                            @if ($item->sellers && $item->sellers->count() > 0)
                            @foreach ($item->sellers as $seller)
                            @if ($seller->pdf_file)
                            <button class="btn btn-sm btn-primary"
                                onclick="showPdfPopup('{{ asset('storage/' . $seller->pdf_file) }}', '{{ basename($seller->pdf_file) }}')">
                                <i class="fas fa-file-pdf"></i> {{ basename($seller->pdf_file) }}
                            </button><br>
                            @else
                            ไม่มีไฟล์ PDF
                            @endif
                            @endforeach
                            @else
                            ไม่มีข้อมูลผู้ขาย
                            @endif
                        </td>

                        <td>{{ $item->created_at->format('d/m/y') }}</td>
                        <td>{{ $item->devilvery_time }}</td>
                        <td>
                            @if ($item->status != 'Complete')
                            <a href="{{ route('page.confirm', $item->id) }}" role="button"
                                class="btn btn-sm btn-danger">
                                <i class="fas fa-sync-alt"></i> แปลงไฟล์
                            </a>

                            <!-- ตรวจสอบค่า template_source เพื่อตรวจสอบว่าจะพาไปหน้า edit หรือ editk -->
                            @if ($item->template_source == 'formk')
                            <a href="{{ url("page/{$item->id}/editk") }}" role="button" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                            @else
                            <a href="{{ url("page/{$item->id}/edit") }}" role="button" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                            @endif

                            <!-- ปุ่มสำหรับดูข้อมูลเพิ่มเติม -->
                            <button type="button" class="btn btn-sm btn-info"
                                onclick="showInfo({{ json_encode($item) }})">
                                <i class="fas fa-eye"></i> ดูข้อมูล
                            </button>
                            @else
                            <button class="btn btn-sm btn-secondary" disabled>
                                <i class="fas fa-sync-alt"></i> แปลงไฟล์
                            </button>
                            <button class="btn btn-sm btn-secondary" disabled>
                                <i class="fas fa-edit"></i> แก้ไข
                            </button>
                            <button type="button" class="btn btn-sm btn-info"
                                onclick="showInfo({{ json_encode($item) }})">
                                <i class="fas fa-eye"></i> ดูข้อมูล
                            </button>
                            @endif
                        </td>
                        <td>
                            @if ($item->status == 'Complete')
                            <span class="badge bg-success">Completed</span>
                            @else
                            <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            <!-- Pagination -->
            {{ $info->links('pagination::bootstrap-4') }}
        </div>

        <!-- SweetAlert2 CSS และ JS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.2.8/pdfobject.min.js"></script>

        <!-- สคริปต์แสดงข้อมูลเพิ่มเติมใน SweetAlert2 -->
        <script>
        function showPdfPopup(pdfUrl, fileName) {
            console.log(pdfUrl); // ตรวจสอบ URL ของไฟล์ PDF
            Swal.fire({
                title: fileName,
                html: `<div id="pdfViewer" style="height: 500px;"></div>`,
                width: '80%',
                showCloseButton: true,
                showCancelButton: false,
                focusConfirm: false,
                confirmButtonText: 'ปิด',
                didOpen: () => {
                    PDFObject.embed(pdfUrl, "#pdfViewer");
                }
            });
        }


        function showInfo(item) {
            let htmlContent = `
                        <p><strong>ID:</strong> ${item.id}</p>
                        <p><strong>ประเภท:</strong> ${item.methode_name}</p>
                        <p><strong>เหตุผล:</strong> ${item.reason_description}</p>
                        <p><strong>คณะ:</strong> ${item.office_name}</p>
                        <p><strong>วันที่สร้างไฟล์:</strong> ${new Date(item.created_at).toLocaleDateString()}</p>
                        <p><strong>ระยะเวลาแล้วเสร็จ:</strong> ${item.devilvery_time}</p>
                    `;

            if (item.products && item.products.length) {
                htmlContent += `<strong>ผลิตภัณฑ์:</strong><ul>`;
                item.products.forEach(product => {
                    htmlContent +=
                        `<li>${product.product_name} - จำนวน: ${product.quantity} ${product.unit} - ราคา: ${product.product_price}</li>`;
                });
                htmlContent += `</ul>`;
            }

            if (item.sellers && item.sellers.length) {
                htmlContent += `<strong>ผู้ขาย:</strong><ul>`;
                item.sellers.forEach(seller => {
                    htmlContent +=
                        `<li>${seller.seller_name} - ที่อยู่: ${seller.address} - หมายเลขผู้เสียภาษี: ${seller.taxpayer_number}</li>`;
                });
                htmlContent += `</ul>`;
            }

            if (item.committeemembers && item.committeemembers.length) {
                htmlContent += `<strong>คณะกรรมการ:</strong><ul>`;
                item.committeemembers.forEach(member => {
                    htmlContent += `<li>${member.member_name} - ตำแหน่ง: ${member.member_position}</li>`;
                });
                htmlContent += `</ul>`;
            }

            if (item.bidders && item.bidders.length) {
                htmlContent += `<strong>ผู้เสนอราคา:</strong><ul>`;
                item.bidders.forEach(bidder => {
                    htmlContent += `<li>${bidder.bidder_name} - ตำแหน่ง: ${bidder.bidder_position}</li>`;
                });
                htmlContent += `</ul>`;
            }

            if (item.inspectors && item.inspectors.length) {
                htmlContent += `<strong>ผู้ตรวจสอบ:</strong><ul>`;
                item.inspectors.forEach(inspector => {
                    htmlContent +=
                        `<li>${inspector.inspector_name} - ตำแหน่ง: ${inspector.inspector_position}</li>`;
                });
                htmlContent += `</ul>`;
            }

            if (item.mores && item.mores.length) {
                htmlContent += `<strong>ข้อมูลเพิ่มเติม:</strong><ul>`;
                item.mores.forEach(more => {
                    htmlContent += `
                                <li><strong>ใบเสนอราคา:</strong> ${more.price_list}</li>
                                <li><strong>เอกสารขออนุมัติ:</strong> ${more.request_documents}</li>
                                <li><strong>แหล่งที่มาของราคากลาง 1:</strong> ${more.middle_price_first}</li>
                                <li><strong>แหล่งที่มาของราคากลาง 2:</strong> ${more.middle_price_second}</li>
                                <li><strong>แหล่งที่มาของราคากลาง 3:</strong> ${more.middle_price_third}</li>`;
                });
                htmlContent += `</ul>`;
            }

            Swal.fire({
                title: `<strong>รายละเอียด</strong>`,
                icon: 'info',
                html: htmlContent,
                showCloseButton: true,
                showCancelButton: false,
                focusConfirm: false,
                confirmButtonText: 'ปิด',
                customClass: {
                    htmlContainer: 'text-start'
                },
                width: '850px'
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '{{ session('
                success ') }}',
                timer: 3000,
                showConfirmButton: false
            });
            @endif
        });
        </script>

    </div>

    <div class="card-footer d-flex justify-content-between">
        <a href="{{ url('/page') }}" role="button" class="btn btn-danger">
            <i class="fas fa-arrow-left"></i> กลับ
        </a>
    </div>
</div>

<!-- Font Awesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
.btn {
    transition: transform 0.3s;
}

.btn:hover {
    transform: scale(1.1);
    /* ซูมเข้าขณะ hover */
}
</style>
@endsection