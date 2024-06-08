"use strict";
$(document).ready(function() {
    if(graphData.module == 0){
        /*Bar chart start*/
        nv.addGraph(function() {
            var chart = nv.models.discreteBarChart()
                .x(function(d) {
                    return d.label }) //Specify the data accessors.
                .y(function(d) {
                    return d.value })
                .staggerLabels(true) //Too many bars and not enough room? Try staggering labels.
                /* .tooltips(false)    */ //Don't show tooltips
                .showValues(true) //...instead, show the bar value right on top of each bar.
                /*     .transitionDuration(350)*/
            ;

            d3.select('#main').append('svg')
                .datum(barData())
                .call(chart);

            nv.utils.windowResize(chart.update);

            return chart;
        });

        //Each bar represents a single discrete quantity.
        function barData() {
            let graph = [{
                key: "",
                values: []
            }];
            Object.keys(graphData).forEach(el => {
                graph[0]['values'].push({
                    "label": el,
                    "value": graphData[el],
                    "color": "#007BB6"
                });
            });
            return graph;
        }
    } else {
        dashboardEcharts();
        $(window).on('resize',function() {
            dashboardEcharts();
        });
    }
});

function dashboardEcharts() {
    /*line chart*/
    var myChart = echarts.init(document.getElementById('main')); 
    let axis_data = [];
    let graph_data = [];
    Object.keys(graphData).forEach(el => {
        axis_data.push(el);
        graph_data.push(graphData[el]);
    });
    var option = {
            
        tooltip : {
            trigger: 'axis'
        },
        toolbox: {
            show : false,
            feature : {
                mark : {show: true},
                dataView : {show: true, readOnly: false},
                magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                restore : {show: true},
                saveAsImage : {show: true}
            }
        },
        calculable : true,
        xAxis : [
            {
                type : 'category',
                splitLine: {
                            show: false
                        },
                boundaryGap : false,
                data : axis_data
            }
        ],
        color:  ["#007BB6"],
        yAxis : [
            {
                type : 'value',
                splitLine: {
                            show: false
                        }
            }
        ],
        series : [
            {
                name:'Пополнения',
                // type:'line',
                type:'bar',
                smooth:true,
                itemStyle: {normal: {areaStyle: {type: 'macarons'}}},
                data: graph_data
            }
        ]
    };

    myChart.setOption(option); 
}