define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {


    //设置弹窗宽高
    Fast.config.openArea = ['70%', '80%'];
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'dxs/project/index' + location.search,
                    add_url: 'dxs/project/add',
                    edit_url: 'dxs/project/edit',
                    del_url: 'dxs/project/del',
                    multi_url: 'dxs/project/multi',
                    import_url: 'dxs/project/import',
                    table: 'dxs_project',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3'),"4":__('Status 4')}, formatter: Table.api.formatter.status},
                        {field: 'showswitch', title: __('Showswitch'), searchList: {"1":__('Yes'),"0":__('No')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'matchdate', title: __('Matchdate'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'enter_begintime', title: __('Enter_begintime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'enter_endtime', title: __('Enter_endtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'limit', title: __('Limit')},
                        {field: 'virtual_num', title: __('Virtual_num')},
                        {field: 'coverimage', title: __('Coverimage'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        {field: 'onlinepayswitch', title: __('Onlinepayswitch'), searchList: {"1":__('Yes'),"0":__('No')}, table: table, formatter: Table.api.formatter.toggle},
                        // {field: 'groupswitch', title: __('Groupswitch'), searchList: {"1":__('Yes'),"0":__('No')}, table: table, formatter: Table.api.formatter.toggle},
                        // {field: 'scorebgimage', title: __('Scorebgimage'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        // {field: 'scoretime', title: __('Scoretime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'scoreswitch', title: __('Scoreswitch'), searchList: {"1":__('Yes'),"0":__('No')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'views', title: __('Views')},
                        // {field: 'weigh', title: __('Weigh'), operate: false},
                        // {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        // {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    'name': 'project_task',
                                    'title': function (row) {
                                        return '赛事任务管理[' + row.title + ']';
                                    },
                                    'icon': 'fa fa-edit',
                                    'text': '任务管理',
                                    'classname': 'btn btn-xs btn-warning btn-dialog',
                                    'url': 'dxs/task/index/project_id/{ids}',
                                    'extend': 'data-area=\'["80%","80%"]\''
                                }, {
                                    'name': 'form_field',
                                    'title': function (row) {
                                        return '参赛队伍[' + row.title + ']';
                                    },
                                    'icon': 'fa fa-list',
                                    'text': '参赛队伍',
                                    'classname': 'btn btn-xs btn-success btn-dialog',
                                    'url': 'miniform/fields/index/source/{table}/source_id/{ids}',
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
                url: 'dxs/project/recyclebin' + location.search,
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
                                    url: 'dxs/project/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'dxs/project/destroy',
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
