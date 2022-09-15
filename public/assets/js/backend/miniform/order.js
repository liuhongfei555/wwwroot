define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'miniform/order/index' + location.search,
                    // add_url: 'miniform/order/add',
                    edit_url: 'miniform/order/edit',
                    del_url: 'miniform/order/del',
                    multi_url: 'miniform/order/multi',
                    import_url: 'miniform/order/import',
                    table: 'miniform_order',
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
                        {field: 'orderid', title: __('Order_id'), operate: 'LIKE'},
                        {field: 'project_id', title: __('Project_id'), formatter: Table.api.formatter.search},
                        {field: 'project.title', title: __('Project_title'), operate: false},
                        {field: 'user_id', title: __('User_id'), formatter: Table.api.formatter.search},
                        {field: 'user.nickname', title: __('Nickname'), operate: false},
                        {field: 'diyform_id', title: __('Diyform_id')},
                        {field: 'amount', title: __('Amount'), operate: 'BETWEEN'},
                        {field: 'payamount', title: __('Payamount'), operate: 'BETWEEN'},
                        {field: 'ip', title: __('Ip'), operate: 'LIKE', formatter: Table.api.formatter.search},
                        {field: 'paytype', title: __('Paytype'), operate: 'LIKE'},
                        {field: 'paytime', title: __('Paytime'), operate: 'RANGE', addclass: 'datetimerange', autocomplete: false, formatter: Table.api.formatter.datetime},
                        {field: 'memo', title: __('Memo'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"created": __('Status created'), "paid": __('Status paid'), "expired": __('Status expired'), "refunded": __('Status Refunded'), "refunding": __('Refunding')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', autocomplete: false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate: 'RANGE', addclass: 'datetimerange', autocomplete: false, formatter: Table.api.formatter.datetime},
                        {
                            field: 'operate', title: __('Operate'), clickToSelect: false, table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate, buttons: [{
                                name: 'refunded',
                                title: __('退款状态查询'),
                                text: '退款状态查询',
                                classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                icon: 'fa fa-search',
                                url: 'miniform/order/refunded',
                                success: function (data, ret) {
                                    console.log(data, ret)
                                    //如果需要阻止成功提示，则必须使用return false;
                                    //return false;
                                },
                                error: function (data, ret) {
                                },
                                hidden: function (row) {
                                    return row.status != 'refunded';
                                }
                            }]
                        }
                    ]
                ]
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
            }
        }
    };
    return Controller;
});
