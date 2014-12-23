/**
 * @description : script for result page
 *
 * @author : Kegui Huang
 * 
 */
 
var minisetDOID = [];
var dolistData; //cache the data
var do_data = {};
var cw_has_cache = false;

var edge_checked = {};
    edge_checked.d2g = true;

var item_checked = {};

var existed_nodes = [];
var existed_edges = [];

$(function(){
    $('#pageTab a:first').tab("show");
    creat_dl_content(GENES);
    
    //TABS SWITCH
    $('#pageTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        var id = $(this).attr("href").substring(1);
        
        if($("#" + id).find("." + id).length > 0){
            if( id == "nw"){
                setTimeout(function(){
                    initFilterCytoscapeweb();
                },800);
                
            }else{
                //RESET NW LAYOUT WHEN TAB NOT ON NW 
                var vis_network = vis.networkModel();
                var vis_visualStyle = vis.visualStyle();
                vis.draw({
                    network: vis_network,
                    visualStyle : vis_visualStyle,
                    layout : {
                        name : "ForceDirected"
                    }
                }); 
                $("#layout option").eq(1).attr('selected', 'true');
            }
            return;
        }   
        switch(id){
            case "dl":{
                creat_dl_content(GENES);
            }
            break;
            case "ms":{
                creat_ms_content(GENES);
            }
            break;
            case "nw":{
                creat_nw_content(GENES);
            }
            break;
        }
    });
    
    //COLOR CHECKBOX CLICK TO ADD EDGES 
    $('.chooseitem input').live('click',function(){
        var chooseType = $(this).val();
        addCytoscapewebElements(this ,chooseType);
        var len = $(".chooseitem").length ;
        for(var i = 0; i < len; i++){
            var type = $('.chooseitem input:eq('+i+')').attr('value').replace(/\s/g,'');
            var checked = true;
            if ($('.chooseitem input:eq('+ i +')').attr("checked") !== "checked"){
                checked = false;
               
            }
            edge_checked[type] = checked;
        }
        _lastFilter = function(edge){
            return edge_checked[edge.data.egc.replace(/\s/g,'')];
        };
        vis.filter("edges", _lastFilter, true);
    });
    
    //EXPORT NETWORK
    $('#exportBtn').live('click',function(){

        var index = $('#export').get(0).selectedIndex,
            format = $('#export').get(0).options[index].text,
            network,script;

        //EACH TYPE USE CORRESPAND API TO FETCH THE DATA 
        script = "vis." + format + "()"; //network = vis.png(); 
        network = eval(script);
       
        //POST DATA TO SERVER AND WRITE IN FILE FOR DOWNLOAD
        $.ajax({
            type:"POST",
            url:"import.php",
            data:{
                data:network,
                type:format
            },
            success:function(){
                window.open("./export.php?type=" + format,"_self");
            }
        });
        //vis.exportNetwork(format, './export.php?type='+format);   
    });
    
    //HOVER EFFECT
    $('.chooseitem span').live('mouseover',function(){
        $(this).parent().parent().css('background-color', '#F6F6F6');
        var type = $.trim($(this).parent().find('input').val());
        
        if($(this).parent().find("input").attr("checked") !== "checked"){
            addFilterCytoscapeweb(type ,true);
        }

        var edges = vis.edges();
        var nodes = vis.nodes();
        var bypass = {
            nodes:{},
            edges:{}
        };        
        var props = {
            opacity : 1
        };
        var _props = {
            opacity: 0.08
        };
        var nodesArray = [];
        var edgesArray = [];
        $.each(nodes,function(i,e){
            var n = e.data.id;
            bypass["nodes"][n] = _props;         
        });
        $.each(edges,function(i,e){
            var c = e.data.id;
            bypass["edges"][c] = _props;    
            if($.trim(e.data.egc) == type){
                var t = e.data.target;
                var s = e.data.source;             
                nodesArray.push(t);
                nodesArray.push(s);
                edgesArray.push(c);              
            }          
        });
        vis.visualStyleBypass(bypass);
        $.each(nodesArray,function(i,e){
            bypass["nodes"][e] = props;           
        });
        $.each(edgesArray,function(i,e){
            bypass["edges"][e] = props;           
        });
        vis.visualStyleBypass(bypass);         
    });
    
    //HOVER EFFECT
    $('.chooseitem span').live('mouseout',function(){
        $(this).parent().parent().css('background-color', '#FFF');
        
        var type = $.trim($(this).parent().find('input').val());
        if($(this).parent().find("input").attr("checked") !== "checked"){
            addFilterCytoscapeweb(type ,false);
        }
        
        var edges = vis.edges();
        var nodes = vis.nodes();
        var props = {
            opacity : 1
        };
        var bypass = {
            nodes:{},
            edges:{}
        };
        $.each(nodes,function(i,e){
            var n = e.data.id;
            bypass["nodes"][n] = props;         
        });  
        $.each(edges,function(i,e){
            var c = e.data.id;
            bypass["edges"][c] = props;         
        });        
        vis.visualStyleBypass(bypass);
    });
});

function creat_dl_content(genes){
    $("#dl").find(".loader").show();
    $.ajax({
         type :'post',
         url:'get_mappings_ajax.php',
         dataType:'json',
         data:{
            type:'dl',
            gene:genes  
         },
         async: false,
         success:function(data){
            $("#dl").find(".loader").hide();
            dolistData = data;
            var contents = "<div class='dl'>" +
            "<div id='title'><p>Terms with scores get from GDOList for gene : " + genes + "</p></div>"+
            "<div id='dolist'><div id='dolist_header' class='header'>GDOList</div></div>"+
            "</div>";
            $('#dl').append(contents);
            var dolist = makeTableItems(data ,"dolist");
            $('#dolist').append(dolist);
            adjustStyle("dolist");
        },
        error:function(){
            
        }
    }); 
}

function creat_ms_content(genes){
    $("#ms").find(".loader").hide();
    $.ajax({
        type :'post',
        url:'get_mappings_ajax.php',
        dataType:'json',
        data:{
            type:"ms",
            gene:genes  
        },
        async: false,
        success:function(data){
            $("#ms").find(".loader").hide();
            var contents = "<div class = 'ms'>"+
            "<div id='title'><p>Terms with scores get from MiniGDOList for gene: " + genes + "</p></div>"+
            "<div id='miniset'><div id='miniset_header' class='header'>MiniGDOList</div></div>"+
            "</div>";
            $('#ms').append(contents);
            var miniset = makeTableItems(data ,"miniset");
            $('#miniset').append(miniset);
            adjustStyle("miniset");
        }
        ,
        error:function(){
            
        }
    });
}

//CREAT NW CONTENT
function creat_nw_content(genes){
    $("#nw").find(".loader").show();
    var chooseHtml = '<div class="choosebox network_content pane" id="choosebox">' +
    '<div id="chooseTitle"><span>Choose Interaction Type</span></div>' +
    '<div class="chooseitem">' +
    '<div class="choosecolor" style="height:30px;line-height:30px;color:#999999;"><label class="checkbox"><input class="choosetype" type="checkbox" name="reltype" value ="d2g" checked="checked"><span>Gene Disease</span></label>'+
    '</div>' +
    '</div>' +
    '<div class="chooseitem">'+
    '<div class="choosecolor" style="height:30px;line-height:30px;color:blue;"><label class="checkbox"><input class="choosetype" type="checkbox" name="reltype" value ="genetic_interactions" /><span>Genetic Interactions</span></label>'+
    '</div>' +
    '</div>' +
    '<div class="chooseitem">'+
    '<div class="choosecolor" style="height:30px;line-height:30px;color:#cf2aea;"><label class="checkbox"><input class="choosetype" type="checkbox" name="reltype" value ="physical_interactions" /><span>Physical Interactions</span></label>'+
    '</div>' +
    '</div>' +
    '<div class="chooseitem">'+
    '<div class="choosecolor" style="height:30px;line-height:30px;color:green;"><label class="checkbox"><input class="choosetype" type="checkbox" name="reltype" value ="co_expression" /><span>Coexpression</span></label>'+
    '</div>' +
    '</div>' +
    '<div class="chooseitem">'+
    '<div class="choosecolor" style="height:30px;line-height:30px;color:#f4550b;"><label class="checkbox"><input class="choosetype" type="checkbox" name="reltype" value ="co_localization" /><span>Colocalization</span></label>'+
    '</div>' +
    '</div>' +
    '<div class="chooseitem">'+
    '<div class="choosecolor" style="height:30px;line-height:30px;color:#00CCFF;"><label class="checkbox"><input class="choosetype" type="checkbox" name="reltype" value ="shared_protein_domains" /><span>Shared Protein Domains</span></label>'+
    '</div>' +
    '</div>' +
    '<div id="cw_loader"></div>'+
    '</div>';

    var cw_title = "<div id='cytoscapeweb_title'>" +
    "<p>Terms get from GDOList for searched genes " + genes + " along with gene interactions involved in cytoscapeweb.</p></div>" +
    '<div class="tools network_content">' +
    '<span>Change Network Layout: </span>' +
    '<select name="layout" id="layout" onchange="layout(this)">' + 
    '<option value="0">---Select Layout Type---</option>' + 
    '<option value="1">ForceDirected</option>' + 
    '<option value="2">Circle</option>' + 
    //'<option value="3">Radial</option>' + 
    '<option value="4">Tree</option>' + 
    '</select>'+
    '&nbsp;&nbsp;&nbsp;&nbsp;<span>Select the format to export: </span>'+
    '<select name="export" id="export" >' + 
    '<option value="">xgmml</option>' +
    '<option value="">png</option>' + 
    '<option value="">sif</option>' + 
    '<option value="">svg</option>' + 
    '<option value="">pdf</option>' +
    '<option value="">graphml</option>' + 
    '</select>'+
    '&nbsp;&nbsp;<input id="exportBtn" type="button" class="btn" value="Export"></input>' + 
    '</div>' ;
    var cytoscapeweb = "<div id='cytoscapeweb' class='nw'></div>";
   
    var html = cw_title  +  chooseHtml + cytoscapeweb;
    $("#nw").append(html);
    $("#cw_loader").hide();
    $("#cytoscapeweb").height(600);
    
    var data = makeCytoscapewebData(dolistData);
    makeCytoscapeWebView("cytoscapeweb", data.cw_node_data, data.cw_edge_data);
    $("#nw").find(".loader").hide();
    //initFilterCytoscapeweb();
}

//CREATE Cytoscapeweb DATA BY CACHE
function makeCytoscapewebData(data){
    
    var cytoscapewebData = {};
    var nodes_array = [];
    var edges_array = [];
    var existed_nodes = [];
    $.each(data, function(gene ,item){
        //query node
        var query_node = {};
        query_node.id = gene;
        query_node.num = item.length;
        query_node.ngc = "q";
        nodes_array.push(query_node);
        
        //result node
        $.each(item, function(i,e){  
            var do_node = {};
            
            if(!_.contains(existed_nodes,e.term)){
                existed_nodes.push(e.term);
                do_node.id = e.term;
                do_node.num = 2;
                do_node.ngc = "r";
                nodes_array.push(do_node);
            }

            var dg_edge = {};
            dg_edge.id = e.term + "_" + gene;
            dg_edge.egc = "d2g";
            dg_edge.target = e.term;
            dg_edge.source = gene;
            dg_edge.score = "25"; 
            dg_edge.pvalue = parseFloat(e.score);
            edges_array.push(dg_edge);   
        });
        cytoscapewebData.cw_node_data = nodes_array;
        cytoscapewebData.cw_edge_data = edges_array;
    });
    
    $.each(dolistData,function(gene,item){
        var terms = [];
        $.each(item,function(i,e){
            var item = {};
            item.DOID = e.DOID;
            item.term = e.term;
            terms.push(item);
        });
        do_data[gene] = terms;
    });
    
    return   cytoscapewebData;
}

//GET AND ADD EDGES WHEN FIRST CLICK
function addCytoscapewebElements(elem , type){
    var centerY = parseInt($("#cytoscapeweb").height())/2;
    var centerX = parseInt($("#cytoscapeweb").width())/2;
    if(item_checked[type] || type == "d2g")
        return;
    var elements = [];
    var elemLength = 0 ;
    $.ajax({
        type :'post',
        url:'add_cytoscapeweb_ajax.php',
        dataType:'json',
        data:{
            data:do_data,
            type:type
        },
        async: true,
        beforeSend:function(){
            $("#cw_loader").show();
        },
        success:function(data){
            item_checked[type] = true;
            var cw_node = eval('(' + data.cw_node_data + ')');
            var cw_edge = eval('(' + data.cw_edge_data + ')');
            var i = parseInt(10 * Math.random());
            var radius = 230 + parseInt(50 * Math.random());
            $.each(cw_node,function(i,e){    
                if( _.indexOf(existed_nodes,e.id) < 0){
                    existed_nodes.push(e.id);
                    var rand = 0.5 * Math.random() - 0.25;
                    var item = {
                        group: "nodes",
                        x:500 + radius * Math.sin(i * 0.5 + rand),
                        y:300 + radius * Math.cos(i * 0.5 + rand),
                        data:e
                    };
                    elements.push(item);
                    i++;
                }  
            });
            $.each(cw_edge,function(i,e){
                if(_.indexOf(existed_edges,e.id) < 0){
                    existed_edges.push(e.id);
                    var item = {
                        group: "edges",
                        data:e
                    };
                    elements.push(item);
                }
            });
            vis.addElements(elements, true);
            if(elements.length < 0){
                var layoutType = "ForceDirected";
                var vis_network = vis.networkModel();
                var vis_visualStyle = vis.visualStyle();
                vis.draw({
                    network: vis_network,
                    visualStyle : vis_visualStyle,
                    layout : {
                        name : layoutType,
                        options : {
                    //weightAttr:"score"
                    }
                    }
                });    
            }
            $("#cw_loader").hide();
            elemLength = elements.length ;
            if(elemLength < 1){
                $(elem).attr("disabled","disabled");
            }
        }
    });
    
}

//FILTER HOVER EVENT 
function addFilterCytoscapeweb(type ,checked){
        edge_checked[type] = checked;
        var swFilter = function(edge){
            return edge_checked[edge.data.egc.replace(/\s/g,'')];
        };
        vis.filter("edges", swFilter, true);    
    }


//FILTER FOR INITIALIZE
function initFilterCytoscapeweb(){
    var checked;
    edge_checked["d2g"] = true;
    var len = $(".chooseitem").length ;
    for(var i = 1; i < len; i++){
        var type = $('.chooseitem input:eq(' + i + ')').attr('value').replace(/\s/g,'');
        if ($('.chooseitem input:eq(' + i + ')').attr("checked") != "checked"){
            checked = false ;
        }else{
            checked = true;
        }
        edge_checked[type] = checked;
    }
    var initFilter = function(edge){
        return edge_checked[edge.data.egc.replace(/\s/g,'')];
    };
    vis.filter("edges", initFilter, true);       
}

function makeTableItems(data ,type){
    
  //var html = "<table class='table'><tr><th>GENE</th><th>DOID</th><th>DO Term</th><th>Score</th><th>Mapping</th><th width='10%'>PubmedID</th><th width ='40%'>GeneRIF</th></tr>";
    var html = "<table class='table'><tr><th width='5%'>GENE</th><th width='5%'>DOID</th><th width='10%'>DO Term</th><th width='5%'>Score</th><th width='5%'>Mapping</th><th class= 'inner_wraper' style='padding:0px;' cellpadding = 0 cellspacing = 0 colspan = 2 width='70%'><table class='inner_table' cellpadding = '0' cellspacing = '0'><tr><td  class='td_gr' width='15%'>PubmedID</td><td width ='85%'>GeneRIF</td></tr></table></th></tr>";
    $.each(data, function(gene,item){
        if(item.length == 0){
            html += "<tr><td>" + gene + "</td><td colspan ='6'><i>NO MAPINGS FOUND<i></td></tr>";
        }else{
            $.each(item, function(i,e){
                var generifs ="<table class='inner_table' cellpadding = '0' cellspacing = '0'>";
                if(e.GeneRIF.length == 0){
                    generifs += "<tr><td class='td_gr' width='15%'><div class='empty'><p>--</p></div></td><td width='85%'><div class='empty'><p>--</p></div></td></tr>";
                }else{
                    $.each(e.GeneRIF, function(index,info){
                        generifs += "<tr><td class='td_gr' width='15%'><a  target='_blank' href='http://www.ncbi.nlm.nih.gov/pubmed?term=" + info.generif_id + "'>" + info.generif_id + "</a></td><td width='85%'>" + info.text + "</td></tr>";
                    });
                }
                generifs += "</table>";
                html += "<tr id='"+e.DOID+"'><td width='5%'>" +gene+ "</td><td width='5%'>" + e.DOID+ "</td><td width='10%'>" + e.term+ "</td><td width='5%'>" + e.score +"</td><td width='5%'>" + e.direct + "</td><td class= 'inner_wraper' style='padding:0px;' cellpadding = 0 cellspacing = 0 colspan = 2 width='70%'>" + generifs + "</td></tr>"; 
            }); 
        }
        
    });  
    html += "</table>";
    return html;
}

//adjust table style
function adjustStyle(id){
    var trs = $("#" + id).find(".table").eq(0).find("tr"),
        len = trs.length;

    for(var i = 1; i < len; i++){
        var tr = trs[i], 
            inner_wraper = $(tr).find(".inner_wraper").eq(0),
            inner_table = $(inner_wraper).find(".inner_table").eq(0),
            emptyDiv = $(inner_table).find(".empty").eq(0);

        //SET INNER TABLE HEIGHT
        if($(inner_wraper).height() != $(inner_table).height()){
            $(inner_table).height($(inner_wraper).height());
        }

        var tdHeight = $(emptyDiv).parent().height(),
            p = $(tr).find(".empty").find("p");

        //SET EMPTY DIV HEIGHT
        if($(emptyDiv).height() != tdHeight ){
            $(tr).find(".empty").height(tdHeight);
            $(p).css("line-height",$(p).eq(0).height()+"px");
        }

    }
}