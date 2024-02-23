@extends('adminweb.layouts.frontend')

@section('content')
    <section id="section_content" class="order">
        <div class="own-page-header">
            <div class="container">
                <div class="row">
                    <h3 class="pull-left own-h3"><span><img src="/images/icon_order.png"></span>Lịch Sử Gửi Mail</h3>
                </div>
            </div>
        </div>

        <div class="own-page-content">
            <div class="container">
                <div class="row">
                    <div class = "tab-content order_detail_tab_content voucher">
                        <div id = "redeemed_tb" class = "tab-pane fade in active">
                            <div class = "panel panel-default own-panel-lg-border">
                                <div class = "panel-heading own-panel-heading">
                                    <div class = "title">
                                        <h4 class = "own-h4">Tìm</h4>
                                    </div>
                                </div>

                                <div class="panel-body own-panel-body">
                                    <div class = "row wrap-tbl">
                                    <form class="form" method="get" action="" id="form-search">
                                        <div class="container">
                                            <div class="col-md-4 d-flex flex-column">
                                                <label>Ngày gửi</label>
                                                <div class="input-group">
                                                    <input style="background-color: white;" type="text" class="form-control" autocomplete="off" name="daterange_fromto"
                                                            placeholder="Từ - Đến" value="{{$currentSearchParams['daterange_fromto'] ?? ''}}" readonly="readonly"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4 d-flex flex-column">
                                                <label>Email <span style="color:red;">*</span></label>
                                                <input type="text" name="email" value="{{ $currentSearchParams['email'] ?? ''}}" class="form-control">
                                            </div>
                                            
                                            <div class="col-md-4 d-flex flex-column">
                                                <label>Trạng thái</label>
                                                <select name="status" autocomplete="off" id="status"                                                             class="form-control">
                                                    <option value="-1" selected="true">Tất cả</option>
                                                    <option {{ @$_GET['status'] === "1" ? 'selected' : '' }} value="1">
                                                        Thành công
                                                    </option>
                                                    <option {{ @$_GET['status'] === "0" ? 'selected' : '' }} value="0">
                                                        Thất bại
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="pull-right mr-1">
                                                <a href="{{ route('adminweb.email-history.list') }}" class="btn btn-danger mt-3">Hủy tìm kiếm</a>
                                                <button type="submit" class="btn btn-primary mt-3" id="btn-search" disabled>Tìm kiếm</button>
                                            </div>
                                            <div class="clearfix m-2"></div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="page" value="{{ $currentSearchParams['page'] }}" id="url_current_page">
                                    <input type="hidden" name="limit" value="{{ $currentSearchParams['limit']}}" id="url_limit">
                                    </form>
                                </div>
                            </div>

                            <div class = "panel panel-default own-panel-lg-border">       
                                <div class="panel-body own-panel-body">
                                    <div class = "row">
                                        <table class="table own-table table-hover table-responsive table-bordered" id="tb_bundle">
                                            <thead>
                                                <tr>
                                                    <th>Stt</th>
                                                    <th>Email</th>
                                                    <th>Template</th>
                                                    <th>Payload</th>
                                                    <th>Ngày gửi</th>
                                                    <th>Trạng thái</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @if($SalesClientMgmtEmployeeList->count() > 0)
                                                @foreach($SalesClientMgmtEmployeeList as $row)
                                                        <tr>
                                                            <th scope="row">{{ $start_number }}</th>
                                                            <td >{{$row->receiver}} </td>
                                                            <td >{{$row->template}} </td>
                                                            <td><div style="max-width:450px; word-wrap: break-word;">{{$row->payload}}</div></td>
                                                            <td >{{ date('d-m-Y H:i:s', strtotime($row->created_at)) }}</td>
                                                            <td >
                                                            @if($row->status == 1)
                                                                    Thành công
                                                            @else
                                                                    Thất bại
                                                            @endif
                                                            </td>
                                                            <td>
                                                            
                                                                <a style="width: 100%;border: 1px solid black; color: black;"  target="_blank" href="{{route('adminweb.email-history.view',['sreport_id'=> Crypt::encrypt($row->id)])}}" class="btn btn-white mt-3">Xem trước</a>
                                                              
                                                            </td>
                                                        </tr>
                                                        @php 
                                                            $start_number = $start_number + 1;
                                                        @endphp
                                                    @endforeach
                                            @else
                                            <tr>
                                                <td colspan="8">
                                                    <p>Không có dữ liệu</p>
                                                </td>
                                            </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                        <!--end table-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <!-- phân trang -->
                    <input type="hidden" value="{{ $currentUrl }}" id="url_email_history">

                        
                    <!-- Modal Re-SendEmail -->
                    <div class="modal fade reSendEmailModal" id ="reSendEmailModal" tabindex="-1" role="dialog"
                        aria-labelledby="mySmallModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title pull-left">Confirm Re-Send Email</h4>
                                    <button type="button" class="close pull-right" aria-label="Close" onclick="hideReSendModal()">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="alert alert-danger error" style="display:none;">

                                </div>
                                <div class="modal-body text-danger">
                                    Bạn Có chắc chắn muốn gửi lại Email ? <br>
                                    <br><br>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" onclick="confirmReSend();">Yes</button>
                                    <button type="button" class="btn btn-default" onclick="hideReSendModal()" data-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bottom_pagination">
                        <div class="pull-left">
                            <label>{{ trans('content.view') }}
                                <select class="form-control selectpicker own-custom-select limit" id="select-state-list">
                                    @foreach($number_search_list as $number)
                                        <option value="{{ $number }}" @if(isset($currentSearchParams) && $currentSearchParams['limit'] == $number) selected @endif>{{ $number }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        <div class="pull-right">
                            @if (!empty($SalesClientMgmtEmployeeList))
                                {{ $SalesClientMgmtEmployeeList->appends(request()->input())->links('pagination::default') }}
                            @endif
                        </div>
                        <div class="pull-right">

                            <div class="pagination_simple">
                                <ul class="pagination">
                                        @if (Request::has('page') && Request::get('page') > 1)
                                                <li class="paginate_button previous">
                                                <a href="{{ $previousUrl }}"><span class="page-link">Previous page</span></a></li>
                                            @endif
                                            @if ($nextUrl)
                                                <li class="paginate_button previous">
                                                <a href="{{ $nextUrl }}"><span class="page-link">Next page</span></a></li>
                                            @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <span id="msg"></span>
    </section>

    <div class="loadingAjax">
        <div id='img-loading' class='uil-spin-css' style="-webkit-transform:scale(0.4)"><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div></div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
         let reSendEmailModal = $("#reSendEmailModal");
         let request_id = '';

         function hideReSendModal() {
            reSendEmailModal.modal('hide');
        }

        function showreSendEmailModal(id) {
            request_id = id;
            reSendEmailModal.modal('show');
        }

        function confirmReSend() {
            $('.loadingAjax').css({'display':'block'});
            $.ajax({
                url: '{{route('adminweb.email-history.send_email')}}',
                type: 'POST',
                dataType: 'json',
                data:{
                    request_id: request_id,
                },
                success: function(response){
                    console.log(response);
                    if(response.stt == 1){
                        toastr.success(response.msg);
                        let currentUrl = $('#url_email_history').val();
                        setTimeout(function() {
                            window.location.replace(currentUrl);
                        }, 3000);
                    }else{
                        toastr.error(response.msg);
                    }
                },
                error : function(request, status, error) {
                    $('.loadingAjax').css({'display':'none'});
                    toastr.error('Có lỗi xảy ra trong quá trình tính toán hệ thống. Vui lòng thử lại!');
                }
            }).done(function(){
                $('.loadingAjax').css({'display':'none'});
            });
            reSendEmailModal.modal('hide');
        }

    
        $(document).ready(function(){
            let dateRangeSavedTarget = $('input[name="daterange_fromto"]');
            dateRangeSavedTarget.daterangepicker({
                autoUpdateInput: false,
                minDate: 0,
                maxDate: moment(),
                locale: {
                    format: 'DD/MM/YYYY',
                },
                "opens": "left",
            });

            dateRangeSavedTarget.on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                dateFrom = picker.startDate.format('YYYY-MM-DD');
                dateTo = picker.endDate.format('YYYY-MM-DD');
            });

            let limit = null;
            let currentPage = $('#url_current_page').val();

            $('input[name=email]').on( "keyup", function() {
                var email = $(this).val();
                if(email.trim() != ''){
                    $('#btn-search').prop('disabled', false);
                }else{
                    $('#btn-search').prop('disabled', true);
                }
            });

            if($('input[name=email]').val().trim() != ''){
                $('#btn-search').prop('disabled', false);
            }
            
            $('#btn-search').on('click', function (e) {
                $('.loadingAjax').css({'display': 'block'});
                e.preventDefault();
                var email = $('input[name=email]').val();
                var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                if (!regex.test(email.trim())) {
                    $('.loadingAjax').css({'display': 'none'});
                    toastr.error("Email không đúng định dạng");
                } else {
                    $('#form-search').submit();
                }
            });

            window.onbeforeunload = function () {
                
            };

            // Change limit search number
            $(".limit").on("changed.bs.select", function() {
                let url = $('#url_email_history').val();
                let limit = $(this).children('option:selected').val();
                // url = url + '&page=' + currentPage + '&limit=' + limit;
                url = url + '&page=1&limit=' + limit;
                window.location.replace(url);
            });

            // Back to previous page
            $(".pagination").on('click', '.prev_btn', function() {
                let current_page = $(this).attr("data-value");
                let nextPage = parseInt(current_page) - 1;

                let url = $('#url_email_history').val();
                let limit = $('.limit').children('option:selected').val();
                url = url + '&page=' + nextPage + '&limit=' + limit;
                window.location.replace(url);
            });

            // Net to previous
            $(".pagination").on('click','.next_btn',function() {
                let current_page = $(this).attr("data-value");
                let nextPage = parseInt(current_page) + 1;

                let url = $('#url_email_history').val();
                let limit = $('.limit').children('option:selected').val();
                alert(limit);
                url = url + '&page=' + nextPage + '&limit=' + limit;
                window.location.replace(url);
            });
        });
    </script>
@endsection