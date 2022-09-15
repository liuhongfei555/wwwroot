define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'miniform/project/index' + location.search,
                    add_url: 'miniform/project/add',
                    edit_url: 'miniform/project/edit',
                    del_url: 'miniform/project/del',
                    multi_url: 'miniform/project/multi',
                    import_url: 'miniform/project/import',
                    table: 'miniform_project',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'label', title: __('Label'), operate: 'LIKE'},
                        {field: 'category.name', title: __('Category_id'), operate: false},
                        {field: 'people_num', title: __('People_num')},
                        {
                            field: 'price', title: __('Price'), operate: 'BETWEEN', formatter: function (value, row, index) {
                                return value > 0 ? "<span class='text-danger'>" + value + "</span>" : "<span class='text-success'>免费</span>";
                            }
                        },
                        {field: 'registered', title: __('Registered')},
                        {field: 'views', title: __('Views')},
                        {field: 'is_multi', title: __('Is_multi'), searchList: {"1": __('Yes'), "0": __('No')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'is_signin', title: __('Is_signin'), searchList: {"1": __('Yes'), "0": __('No')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'is_verification', title: __('Is_verification'), searchList: {"1": __('Yes'), "0": __('No')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'iscaptcha', title: __('Iscaptcha'), searchList: {"1": __('Yes'), "0": __('No')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'front_back', title: __('Front_back'), searchList: {"1": __('Back'), "0": __('Front')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'images', title: __('Images'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        {field: 'begintime', title: __('Begintime'), operate: 'RANGE', addclass: 'datetimerange', autocomplete: false, formatter: Table.api.formatter.datetime},
                        {field: 'endtime', title: __('Endtime'), operate: 'RANGE', addclass: 'datetimerange', autocomplete: false, formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', autocomplete: false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate: 'RANGE', addclass: 'datetimerange', autocomplete: false, formatter: Table.api.formatter.datetime, visible: false},
                        {field: 'status', title: __('Status'), searchList: {"normal": __('Normal'), "hidden": __('Hidden'), "expired": __('Expired')}, formatter: Table.api.formatter.status},
                        {
                            field: 'operate', title: __('Operate'), clickToSelect: false, table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate, buttons: [
                                {
                                    'name': 'project_field',
                                    'title': function (row) {
                                        return '项目字段管理[' + row.title + ']';
                                    },
                                    'icon': 'fa fa-edit',
                                    'text': '项目字段',
                                    'classname': 'btn btn-xs btn-warning btn-dialog',
                                    'url': 'miniform/fields/index/source/project/source_id/{ids}',
                                    'extend': 'data-area=\'["80%","80%"]\''
                                }, {
                                    'name': 'form_field',
                                    'title': function (row) {
                                        return '项目表单字段[' + row.title + ']';
                                    },
                                    'icon': 'fa fa-list',
                                    'text': '表单字段',
                                    'classname': 'btn btn-xs btn-success btn-dialog',
                                    'url': 'miniform/fields/index/source/{table}/source_id/{ids}',
                                    'extend': 'data-area=\'["95%","95%"]\''
                                },
                                {
                                    'name': 'form_data',
                                    'title': function (row) {
                                        return '项目表单数据[' + row.title + ']';
                                    },
                                    'icon': 'fa fa-list',
                                    'text': '表单数据',
                                    'classname': 'btn btn-xs btn-info btn-dialog',
                                    'url': 'miniform/diyform/index/source/{table}/source_id/{ids}/is_verification/{is_verification}',
                                    'extend': 'data-area=\'["95%","95%"]\''
                                }
                            ]
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'miniform/project/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), align: 'left'},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'miniform/project/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'miniform/project/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            //获取标题拼音
            var si;
            $(document).on("keyup", "#c-title", function () {
                var value = $(this).val();
                if (value != '' && !value.match(/\n/)) {
                    clearTimeout(si);
                    si = setTimeout(function () {
                        Fast.api.ajax({
                            loading: false,
                            url: "miniform/ajax/get_title_pinyin",
                            data: {title: value}
                        }, function (data, ret) {
                            $("#c-table").val(data.pinyin);
                            return false;
                        }, function (data, ret) {
                            return false;
                        });
                    }, 200);
                }
            });
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                $("#c-price,#c-is_verification,#c-is_signin").on("change keyup", function () {
                    var status = parseFloat($("#c-price").val()) > 0 || parseFloat($("#c-is_verification").val()) > 0 || parseFloat($("#c-is_signin").val()) > 0;
                    if (status) {
                        $("#c-is_need_login").val(1);
                        $("[data-input-id='c-is_need_login'] i").removeClass("fa-flip-horizontal text-gray").addClass("fa-toggle-on text-success");
                    }
                    $("[data-input-id='c-is_need_login']").toggleClass("disabled", status);
                });
                $("#c-is_signin").on("change", function () {
                    $("[data-type='signin']").toggleClass("hide", parseFloat($(this).val()) == 0);
                });
                $(document).on("click", "a[data-toggle='addresspicker']", function () {
                    if (!parseInt($(this).data("addon"))) {
                        Layer.alert("请在插件管理安装《地图位置(经纬度)选择》插件后重试");
                        return false;
                    }
                });
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
