define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'dxs/task/index/project_id/' + Config.project_id + '/' + location.search,
                    add_url: 'dxs/task/add/project_id/' + Config.project_id,
                    edit_url: 'dxs/task/edit/project_id/' + Config.project_id,
                    del_url: 'dxs/task/del/project_id/' + Config.project_id,
                    multi_url: 'dxs/task/multi',
                    import_url: 'dxs/task/import',
                    table: 'dxs_task',
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
                        {field: 'qrcode', title: __('Qrcode'), operate: 'LIKE'},
                        {field: 'project_id', title: __('Project_id') ,visible:false, operate:false },
                        {field: 'groupcode', title: __('Groupcode'), operate: 'LIKE'},
                        {field: 'seq', title: __('Seq')},
                        {field: 'coverimage', title: __('Coverimage'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        
                        //{field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        //{field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
