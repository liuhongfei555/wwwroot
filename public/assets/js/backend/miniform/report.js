define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'echarts', 'echarts-theme'], function ($, undefined, Backend, Table, Form, Echarts) {

    var Controller = {

        myChart1: function () {

            // 基于准备好的dom，初始化echarts实例
            let myChart = Echarts.init($('#echarts1')[0], 'walden');

            // 指定图表的配置项和数据
            let option = {
                title: {
                    text: '订单趋势',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['交易额', '订单数']
                },
                toolbox: {
                    show: true,
                    feature: {
                        dataView: {
                            show: true,
                            readOnly: false
                        },
                        magicType: {
                            show: true,
                            type: ['line', 'bar']
                        },
                        restore: {
                            show: true
                        },
                        saveAsImage: {
                            show: true
                        }
                    }
                },
                calculable: true,
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: Config.orderSaleCategory
                },
                yAxis: {},
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                series: [{
                    name: "交易额",
                    type: 'line',
                    smooth: true,
                    areaStyle: {
                        normal: {}
                    },
                    lineStyle: {
                        normal: {
                            width: 1.5
                        }
                    },
                    data: Config.orderSaleAmount
                }, {
                    name: '订单数',
                    type: 'line',
                    data: Config.orderSaleNums,
                    markPoint: {
                        data: [{
                                type: 'max',
                                name: '最大值'
                            },
                            {
                                type: 'min',
                                name: '最小值'
                            }
                        ]
                    },
                    markLine: {
                        data: [{
                            type: 'average',
                            name: '平均值'
                        }]
                    }
                }]
            };
            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);

            $("#form1 .datetimerange").data("callback", function (start, end) {
                var date = start.format(this.locale.format) + " - " + end.format(this.locale.format);
                $(this.element).val(date);
                console.log(date)
                refresh(date);
            });

            var si = null;

            function refresh(date) {
                si && clearTimeout(si);
                si = setTimeout(function () {
                    Fast.api.ajax({
                        url: 'miniform/report/index',
                        data: {
                            date: date,
                            type: 0
                        },
                        loading: false
                    }, function (res) {
                        option.xAxis.data = res.orderSaleCategory;
                        option.series[0].data = res.orderSaleAmount;
                        option.series[1].data = res.orderSaleNums;
                        myChart.clear();
                        myChart.setOption(option, true);
                        return false;
                    });
                }, 50);
            }

            return myChart;
        },
        myTable() {
            // 初始化表格参数配置
            Table.api.init();
            // 表格1
            var table = $("#table");
            table.bootstrapTable({
                url: 'miniform/report/project_sale',
                toolbar: '#toolbar',
                sortName: 'id',
                search: false,
                columns: [
                    [{
                            field: 'title',
                            title: '项目名称',
                            operate: 'LIKE'
                        },
                        {
                            field: 'nums',
                            title: '订单数量',
                            operate:false
                        },
                        {
                            field: 'amount',
                            title: '订单金额',
                            operate:false
                        },
                        {
                            field: 'registered',
                            title: '报名人数'
                        },
                        {
                            field: 'views',
                            title: '浏览人数'
                        }
                    ]
                ]
            });

            // 为表格1绑定事件
            Table.api.bindevent(table);
        },
        index: function () {

            var chart1 = this.myChart1();

            $(window).resize(function () {
                chart1.resize();
            });

            //点击按钮
            $(document).on("click", ".btn-filter", function () {
                var label = $(this).text();
                var obj = $(this).closest("form").find(".datetimerange").data("daterangepicker");
                var dates = obj.ranges[label];
                obj.startDate = dates[0];
                obj.endDate = dates[1];
                obj.clickApply();
            });
            this.myTable();
            Form.api.bindevent($("#form1"));

        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
