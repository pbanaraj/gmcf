function legend(parent, data) {
    legend(parent, data, null);
}

function legend(parent, data, chart, legendTemplate) {
	legendTemplate = typeof legendTemplate !== 'undefined' ? legendTemplate : "<%=label%>";
    parent.className = 'legend';
    var datas = data.hasOwnProperty('datasets') ? data.datasets : data;
    // remove possible children of the parent
    while(parent.hasChildNodes()) {
        parent.removeChild(parent.lastChild);
    }

    var show = chart ? showTooltip : noop;
    datas.forEach(function(d, i) {

        //span to div: legend appears to all element (color-sample and text-node)
        var title = document.createElement('div');
        title.className = 'title';
        parent.appendChild(title);

        var colorSample = document.createElement('div');
        colorSample.className = 'color-sample';
        colorSample.style.backgroundColor = d.hasOwnProperty('strokeColor') ? d.strokeColor : d.color;
        colorSample.style.borderColor = d.hasOwnProperty('fillColor') ? d.fillColor : d.color;
        title.appendChild(colorSample);
        legendNode1="$"+ format2(d.value);
        legendNode2= d.label;
        var label = document.createTextNode(legendNode2);
        var val = document.createTextNode(legendNode1);
        var legendlv = document.createElement('span');
        var legendlv1 = document.createElement('span');
        legendlv.className = 'text-node-label';
        legendlv1.className = 'text-node-val';
        legendlv.appendChild(label);
        legendlv1.appendChild(val);
        title.appendChild(legendlv);
        title.appendChild(legendlv1);
        show(chart, title, i);
    });
}

//add events to legend that show tool tips on chart
function showTooltip(chart, elem, indexChartSegment){
    var helpers = Chart.helpers;

    var segments = chart.segments;
    //Only chart with segments
    if(typeof segments != 'undefined'){
        helpers.addEvent(elem, 'mouseover', function(){
            var segment = segments[indexChartSegment];
            segment.save();
            segment.fillColor = segment.highlightColor;
            chart.showTooltip([segment]);
            segment.restore();
        });

        helpers.addEvent(elem, 'mouseout', function(){
            chart.draw();
        });
    }
}

function noop() {}
function format2(n) {return n.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");}