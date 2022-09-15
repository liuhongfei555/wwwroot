define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'miniform/template_msg/index' + location.search,
                    add_url: 'miniform/template_msg/add',
                    edit_url: 'miniform/template_msg/edit',
                    del_url: 'miniform/template_msg/del',
                    multi_url: 'miniform/template_msg/multi',
                    import_url: 'miniform/template_msg/import',
                    table: 'miniform_template_msg',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'tpl_id', title: __('Tpl_id'), operate: 'LIKE'},
                        {field: 'page', title: __('Page'), operate: 'LIKE'},
                        {field: 'switch', title: __('Switch'), searchList: {"1": __('Yes'), "0": __('No')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        addOrEdit() {         
            $(document).on("fa.event.appendfieldlist", "form[role=form] .btn-append", function (e, obj) {               
                Form.events.selectpicker(obj);
            });
        },
        add: function () {         
            this.addOrEdit();
            Controller.api.bindevent();
        },
        edit: function () {
            this.addOrEdit();            
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                $(document).on("click", ".btn-select-page", function (e, obj) {
                    var that = this;
                    Fast.api.open("miniform/ajax/get_page_list", "选择路径", {
                        callback: function (data) {
                            $(that).parent().prev().val(data).trigger("change");
                        }
                    })
                });
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});