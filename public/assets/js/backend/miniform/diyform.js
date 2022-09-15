define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'miniform/diyform/index/source/' + Config.source + '/source_id/' + Config.source_id + '/' + location.search,
                    edit_url: 'miniform/diyform/edit/source/' + Config.source + '/source_id/' + Config.source_id,
                    del_url: 'miniform/diyform/del/source/' + Config.source + '/source_id/' + Config.source_id,
                    table: 'miniform_form',
                }
            });

            var table = $("#table");

            var columns = [
                {checkbox: true},
                {
                    field: 'id',
                    title: __('Id')
                },
                {
                    field: 'user_id',
                    title: __('User_id'),
                    formatter: Table.api.formatter.search
                },
            ];

            if (Config.fields && Config.fields.length) {
                Config.fields.forEach(item => {
                    if (item.isshowback) {
                        let obj = {
                            field: item.name,
                            title: item.title
                        }
                        if (item.type == 'image' || item.type == 'images') {
                            obj.events = Table.api.events.image;
                            obj.formatter = item.type == 'image' ? Table.api.formatter.image : Table.api.formatter.images;
                        }
                        if (item.type == 'file' || item.type == 'files') {
                            obj.formatter = item.type == 'file' ? Table.api.formatter.file : Table.api.formatter.files;
                        }
                        if (item.type == 'array') {
                            obj.formatter = Controller.api.formatter.arrays;
                        }
                        if (item.type == 'radio' || item.type == 'checkbox' || item.type == 'select' || item.type == 'selects') {
                            obj.formatter = Controller.api.formatter.content;
                            obj.extend = item.content_list;
                            obj.searchList = item.content_list;
                        }
                        columns.push(obj)
                    }
                })
            }
            columns.push.apply(columns, [{
                field: 'createtime',
                title: __('Createtime'),
                operate: 'RANGE',
                addclass: 'datetimerange',
                autocomplete: false,
                formatter: Table.api.formatter.datetime
            },
                {
                    field: 'updatetime',
                    title: __('Updatetime'),
                    operate: 'RANGE',
                    addclass: 'datetimerange',
                    autocomplete: false,
                    formatter: Table.api.formatter.datetime
                }
            ]);
            if (Config.is_signin) {
                columns.push({
                    field: 'signintime',
                    title: __('Signintime'),
                    operate: 'RANGE',
                    addclass: 'datetimerange',
                    autocomplete: false,
                    formatter: Table.api.formatter.datetime
                });
            }

            if (Config.is_verification == 1) {
                columns.push({
                    field: 'verificationtime',
                    title: __('Verificationtime'),
                    operate: 'RANGE',
                    addclass: 'datetimerange',
                    autocomplete: false,
                    formatter: Table.api.formatter.datetime
                },);
            }


            columns.push.apply(columns, [{
                field: 'status',
                title: __('Status'),
                searchList: {
                    "free": __('Free'),
                    "nonpayment": __('Nonpayment'),
                    "paid": __('Paid'),
                    "canceled": __('Canceled')
                },
                formatter: Table.api.formatter.status
            }, {
                field: 'operate',
                title: __('Operate'),
                clickToSelect: false,
                table: table,
                events: Table.api.events.operate,
                formatter: Table.api.formatter.operate
            }]);

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    columns
                ]
            });

            var switchcamera = function (state) {
                if ($("#reader").is(":visible")) {
                    if (state) {
                        $("#startScan").click();
                    } else {
                        $("#stopScan").click();
                    }
                }
            };
            var confirm = function (value) {
                switchcamera(false);
                Fast.api.ajax({
                    url: 'miniform/diyform/verification/source/' + Config.source + '/source_id/' + Config.source_id,
                    data: {
                        verification: value
                    }
                }, function (data, ret) {
                    $("#code").val('');
                    Toastr.success(ret.msg);
                    if (Config.verification_voice) {
                        try {
                            var audio = new Audio(Fast.api.cdnurl('/assets/addons/miniform/img/success.mp3'));
                            audio.play();
                        } catch (e) {

                        }
                    }
                    $(".btn-refresh").trigger("click");
                    switchcamera(true);
                    return false;
                }, function (data, ret) {
                    Toastr.error(ret.msg);
                    switchcamera(true);
                    return false;
                });
            };
            $('.btn-verification').on('click', function () {
                let btn = ["确定", "取消"];
                if (Config.camera_qrcode) {
                    btn.push("<i class='fa fa-qrcode'></i> 摄像头扫码");
                }
                Layer.open({
                    type: 1,
                    title: '核销',
                    area: !!("ontouchstart" in window) ? ["100%", "100%"] : ['500px', '400px'], //自定义文本域宽高
                    id: 'certificationdialog',
                    btn: btn,
                    zIndex: 999,
                    content: Template("verificationtpl", {}),
                    success: function (layero, index) {
                        var input = $('#code', layero);
                        input.focus().on("change keyup", function (e) {
                            if ($("#autosubmit").prop("checked")) {
                                if (input.val().length != 32) {
                                    if (e.type == 'change') {
                                        Toastr.error("核销码长度必须为32位");
                                        switchcamera(true);
                                    }
                                    return;
                                }
                                confirm(input.val());
                            }
                        });
                        setTimeout(function () {
                            input.trigger("focus");
                        }, 1);
                    },
                    yes: function (index, layero) {
                        var input = $('#code', layero);
                        if (input.val().length != 32) {
                            Toastr.error("核销码长度必须为32位");
                            switchcamera(true);
                            return;
                        }
                        confirm(input.val());
                        return false;
                    },
                    btn2: function (index, layero) {
                        $("#stopScan").click();
                    },
                    btn3: function (index, layero) {
                        if (!location.protocol.match(/https/i)) {
                            Layer.alert("只在Https下支持摄像头扫码");
                            return false;
                        }
                        if ($("#reader").is(":visible")) {
                            $("#stopScan").click();
                            $("#reader").addClass("hidden");
                            return false;
                        }
                        try {

                            if (typeof Html5QrcodeScanner !== 'undefined') {
                                _defineProperty(Html5QrcodeScanner, "ASSET_FILE_SCAN", Fast.api.cdnurl("/assets/addons/miniform/img/file-scan.gif"));
                                _defineProperty(Html5QrcodeScanner, "ASSET_CAMERA_SCAN", Fast.api.cdnurl("/assets/addons/miniform/img/camera-scan.gif"));
                            }

                            $("#reader").removeClass("hidden");
                            var html5QrcodeScanner = new Html5QrcodeScanner(
                                "reader", {fps: 5, qrbox: 250});

                            html5QrcodeScanner.render(function (qrCodeMessage) {
                                $("#stopScan").click();
                                $("#code").val(qrCodeMessage).trigger("change");
                            }, function (errorMessage) {

                            });
                            if (!("ontouchstart" in window)) {
                                Layer.style(index, {height: $(window).height() <= 700 ? $(window).height() : "700px"});
                                $(window).resize();
                            }
                        } catch (e) {
                            Toastr.error("当前浏览器暂不支持");
                        }

                        return false;
                    },
                    cancel: function () {
                        $("#stopScan").click();
                    }
                });
                return;
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                content: function (value, row, index) {
                    var extend = this.extend;
                    if (!value) {
                        return '';
                    }
                    var valueArr = value.toString().split(/,/);
                    var result = [];
                    $.each(valueArr, function (i, j) {
                        result.push(typeof extend[j] !== 'undefined' ? extend[j] : j);
                    });
                    return result.join(',');
                },
                arrays(value, row, index) {
                    if (!value) {
                        return '';
                    }
                    var temp = document.createElement("div");
                    temp.innerHTML = value;
                    var output = temp.innerText || temp.textContent;
                    temp = null;
                    return output;
                }
            },
        }
    };
    return Controller;
});
