(function($) {
    "use strict";

	//Yearly Income
	var link = _url + "/dashboard/json_month_wise_income_expense/";
    $.ajax({
        url: link, 
		success: function (data) {
			//console.log(data);
			var json = JSON.parse(data);
            var cashflow = echarts.init(document.getElementById('yearly_income_expense'));

            // specify chart configuration item and data
            var option = {
                /* title : {
					text: 'Income And Expense'
                },*/
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: [$lang_income, $lang_expense]
                },
                toolbox: {
                    show: true,
                    feature: {
                        mark: {show: true},
                        dataView: {
                            show: true,
                            readOnly: false,
                            title: 'Data View',
                            lang: ['Data View', 'Cancel', 'Reset']
                        },
                        magicType: {
                            show: true, title: {
                                line: 'Line',
                                bar: 'Bar',
                                tiled: 'Tiled',
                            }, type: ['line', 'bar', 'tiled']
                        },
                        restore: {show: true, title: 'Reset'},
                        saveAsImage: {
                            show: true, title: 'Save as Image',
                            type: 'png',
                            lang: ['Click to Save']
                        }
                    }

                },
                calculable: true,
                xAxis: [
                    {
                        type: 'category',
                        boundaryGap: false,
                        data: json['Months']
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: $lang_income,
                        type: 'line',
                        color: [
                            '#0dc8de'
                        ],
                        data: json['Income']
                    },
                    {
                        name: $lang_expense,
                        type: 'line',
                        color: [
                            '#fd3c97'
                        ],
                        data: json['Expense']
                    }
                ]
            };

            // use configuration item and data specified to show chart
            cashflow.setOption(option);

        }
    });
	
	//Income Vs Expense Donut Chart
    var link2 = _url + "/dashboard/json_income_vs_expense/";
    $.ajax({
        url: link2,
		success: function (data2) {
            var json2 = JSON.parse(data2);
            var dn_income_expense = echarts.init(document.getElementById('dn_income_expense'));
            var option2 = {
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    orient: 'vertical',
                    x: 'left',
                    data: [$lang_income, $lang_expense]
                },

                calculable: true,
                series: [
                    {
						name: $lang_income_vs_expense,
                        type: 'pie',
                        radius: ['50%', '70%'],
                        color: ['#0dc8de', '#fd3c97'],
                        itemStyle: {
                            normal: {
                                label: {
                                    show: true
                                },
                                labelLine: {
                                    show: true
                                }
                            },
                            emphasis: {
                                label: {
                                    show: true,
                                    position: 'center',
                                    textStyle: {
                                        fontSize: '24',
                                        fontWeight: 'bold'
                                    }
                                }
                            }
                        },
                        data: [
							{value: json2['Income'], name: $lang_income},
                            {value: json2['Expense'], name: $lang_expense},
                        ]
                    }
                ]
            };


            dn_income_expense.setOption(option2);
        }
    });
	

})(jQuery);	