/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(function(){
    
    //console.log(GENES);
    //console.log(COUNT);
    
    if(TYPE == "s" && COUNT == 1){     
        createMappingTable(GENES);
    }else if(TYPE == "s" && COUNT > 1 ){      
        createCytoscapeweb(GENES);
    }else if(TYPE == "p"){
        createPredictTerms(GENES);
    } 
    var contents = "<div id='p_container'></div>";
    $("#main_container").append(contents);
    $('.previous a').live('click',function(){
        var id = $(this).attr("id"); 
        createPreviousPredictData(id);
    });
    
    
    
    
//alert(GENE_IDS);
});

function createMappingTable(gene){
    $.ajax({
         type :'post'
        ,url:'get_mappings_ajax.php'
        ,dataType:'json'
        ,data:{
            gene:gene  
        }
        ,async: false
        ,success:function(data){
            var dolist_data  = data.dolist;
            var miniset_data = data.miniset;
            var contents = "<div id='container'>"+
            "<div id='dolist'><div id='dolist_header' class='header'>DO List</div></div>"+
            "<div id='miniset'><div id='miniset_header' class='header'>miniSet</div></div>"+
            "</div>";
            $('#main_container').append(contents);
            var dolist = makeTableItems(dolist_data);
            var miniset = makeTableItems(miniset_data);
            $('#dolist').append(dolist);
            $('#miniset').append(miniset);
        }
        ,error:function(){
            
        }
    });
}

function makeTableItems(data){
    var html = "<table><tr><th>DO Terms</th><th>Score</th></tr>";
    $.each(data, function(i,e){
        html += "<tr id='"+e.DOID+"'><td>" + e.term+ "</td><td>" +e.score + "</td></tr>";
    });
    html +="</table>";
    return html;
}


function createCytoscapeweb(genes){
    
    $("#main_container").hide();
    $("#cytoscapeweb_container").show();
    $("#cytoscapeweb_container").height($(window).height()-25);
    $("#cytoscapeweb_container").width($(window).width()-25);
    $("#cytoscapeweb_container").layout({
           north__size: 75
         , west__size: .30
         , east__size: .30
         , north__resizable: false
         , north__closable: false               
    });
    $.ajax({
        type :'post',
        url:'get_cytoscapeweb_ajax.php',
        //,url:'data/test.json',
        dataType:'json',
        data:{
            genes:genes  
        },
        async: false,
        success:function(data){
            //console.log(data);
            var miniset = data.miniset_data ;
            var dolist = data.dolist_data;
            var generif = data.generif_data;
            makeCytoscapeWebView("tabs-east", eval('('+miniset.cw_node_data+')'), eval('('+miniset.cw_edge_data+')'));
            makeCytoscapeWebView("tabs-center", eval('('+dolist.cw_node_data+')'), eval('('+dolist.cw_edge_data+')'));
            makeCytoscapeWebView("tabs-west", eval('('+generif.cw_node_data+')'), eval('('+generif.cw_edge_data+')'));
        },
        error:function(a,b,c){
            alert(a);
            alert(b);
            alert(c);
        }
    });
}


function createPredictTerms(genes){
     $.ajax({
         type:'post'
         ,url:'get_predicted_terms_ajax.php'
         ,dataType:'json'
         ,data:{
             genes:genes
         }
         ,async:false
         ,success:function(data){
             var contents = "<div id='container'></div>";
             $("#main_container").append(contents);
             var html = "<table class='p_t'><tr><th class='g_th' width='30%'>GENE</th><th>TERMS</th></tr>";
             $.each(data,function(i,e){
                  html += creatPredictItems(i,e);       
             }); 
              html += "</table>";
             $("#container").append(html);
             var previous ="<div class = 'previous'>"+ 
                 "<a id='p0509' href='#'>p0509</a>|<a id='p0513' href='#'>p0513</a>|<a id='p0913' href='#'>p0913</a>"+
                 "</div>";
             $("#container").append(previous);
         }  
     });
}   
    function creatPredictItems(gene,terms){
        //var html = "<table class='p_t'><tr><th width='30%'>GENE</th><th>TERMS</th></tr>";
       var  html = "<tr><td class='g_td' width='30%' cellpadding='0' cellspacing='0'>" + gene + "</td><td class='t_td' cellpadding='0' cellspacing='0'>";
        html += "<table class='i_p_t'>";
        $.each(terms, function(i,e){
              html += "<tr><td>"+ e +"</td></tr>";            
        });
        html += "</table></td></tr>";
        return html;        
    }
    
    function createPreviousPredictData(type){
       $("#p_container").empty();
       $.ajax({
         type:'post'
         ,url:'get_previous_predicted_ajax.php'
         ,dataType:'json'
         ,data:{
             type:type
         }
         ,async:false
         ,success:function(data){  
            var html = "<table class='p_t'><tr><th class='g_th' width='10%'>GENE</th><th width='20%'>TERMS</th><th width='10%'>PubmedID</th><th width='60%'>Generif</th></tr>";
            $.each(data,function(i,e){
                html += creatPreviousPredictItems(e);        
            });
            html += "</table>";
            $("#p_container").append(html);
         }
       });  
    }
     
    function creatPreviousPredictItems(items){
        for(var key in items){
            var  html = "<tr><td class='g_td' width='10%' cellpadding='0' cellspacing='0'>" + key + "</td><td class='t_td' cellpadding='0' colspan=3 cellspacing='0'>";
            html += "<table class='i_p_t'>";
            $.each(items[key] ,function(i,e){
              html += "<tr><td width='20%'>"+ e.term +"</td><td width='10%'>"+ e.PubmedID +"</td><td width='60%'>"+ e.Generif +"</td></tr>";            
            });
        }
        html += "</table></td></tr>";
        return html;    
    } 
