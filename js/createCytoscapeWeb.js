/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var cw_options = {
    swfPath: "./swf/CytoscapeWeb",
    flashInstallerPath: "./swf/playerProductInstall",
    flashAlternateContent: '<div class="ui-state-error ui-corner-all"><p>This content requires the Adobe Flash Player.</p><p><a href="http://get.adobe.com/flashplayer/"><img width="160" height="41" border="0" alt="Get Adobe Flash Player" src="http://www.adobe.com/macromedia/style_guide/images/160x41_Get_Flash_Player.jpg"></a></p></div>'
};

//Mapping network groups to node colors:
var nodeColorMapper = {
    attrName: "ngc",// nodeGroupCode
    entries: [
    {
        attrValue: "q",
        value: "#FF9086"
        //value:"#FFFFFF"
    },      // disease nodes

    {
        attrValue: "r",
        value: "#6699FF"
    },      // disease nodes
    {
        attrValue: "g",
        value: "#FFFFFF"
    }   
    ]
};

//Mapping network groups to edge colors:
var edgeColorMapper = {
    attrName: "egc",   //edgeGroupCode
    entries: [
    {
        attrValue: "genetic_interactions",
        value: "blue"
    },	//Genetic_interactions

    {
        attrValue: "co_expression",
        value: "green"
    },  //Co_expression//#FBD10A

    {
        attrValue: "co_localization",
        value: "#f4550b"
    },  //Co_localization

    {
        attrValue: "physical_interactions",
        value: "#cf2aea"
    },  //Physical_interactions

    {
        attrValue: "shared_protein_domains",
        value: "#00CCFF"
    },  //Shared_protein_domains
    ]
};

var nodeShapeMapper = {
    attrName: "ngc"// nodeGroupCode
    ,entries: [
    {
        attrValue: "q",
        value:"ELLIPSE"
    },      
    {
        attrValue: "r",
        value: "RECTANGLE"
    },     
    {
        attrValue: "g",
        value: "ELLIPSE"
    }   
    ]
    
};


var cw_style = {
    global: {
        backgroundColor:"#FFF"
    },
    nodes : {
        //shape: "ELIPSE",
        shape: {
            defaultValue: "ELLIPSE",
            discreteMapper: nodeShapeMapper
        },
        color: {
            defaultValue: "#FF9086",
            discreteMapper: nodeColorMapper
        },
        opacity: 1,
        size : {
            defaultValue: 24, 
            continuousMapper : {
                attrName: "num", 
                minValue: 24, 
                maxValue: 48
            }
        },
        borderColor: "#808080",
        borderWidth: 1,
        label: {
            passthroughMapper: {
                attrName: "id"
            }
        },
        
        labelFontWeight: "bold",
        tooltipFontColor: "#ffffff",
        labelGlowColor: "#ffffff",
        labelGlowOpacity: 1,
        labelGlowBlur: 3,
        labelGlowStrength: 20,
        labelHorizontalAnchor: "center",
        labelVerticalAnchor: "bottom",
        selectionBorderColor: "#000000",
        selectionBorderWidth: 2,
        selectionGlowColor: "#ffff33",
        selectionGlowOpacity: 0.6,
        hoverBorderColor: "#000000",
        hoverBorderWidth: 2,
        hoverGlowColor: "#aae6ff",
        hoverGlowOpacity: 0.8
    },
    edges: {
        color: {
            discreteMapper: edgeColorMapper
        },
        width:{
            //defaultValue: 3
            //,
            continuousMapper: {
                attrName: "pvalue",
                minValue: 3,
                maxValue: 7
            }
        }
    }
};

function makeCytoscapeWebView(id, nodes_data, edges_data){
    network_json = {
        dataSchema : 
        {
            nodes : [{
                name : "num", 
                type : "number"
            },{
                name: "ngc",
                type: "string"
            }],
            edges : [{
                name : "score", 
                type : "string"
            },{
                name: "pvalue",
                type: "number"
            },{
                name: "egc",
                type: "string"
            }]
        },
        data : 
        {
            nodes : nodes_data,
            edges : edges_data
        }
    };
    vis = new org.cytoscapeweb.Visualization(id, cw_options);
    vis.ready(function(){
        vis = this;
        if (!vis.hasListener('click', 'nodes')){
            vis.addListener('click', 'nodes', function(event){
                handle_click(event);
            });
        }
        if (!vis.hasListener('click', 'edges')){
            vis.addListener('click', 'edges', function(event){
                handle_click(event);
            });
        }      
    });
    vis.draw({
        network: network_json,
        visualStyle : cw_style,
        panZoomControlVisible: true,
        edgesMerged: false,
        nodeLabelsVisible: true,
        edgeLabelsVisible: false,
        nodeTooltipsEnabled: false,
        edgeTooltipsEnabled: false,
        layout : {
            name : "ForceDirected",
            options : {
                //"weightAttr":"score"
            }
        }
    });
}

function handle_click(event) {
    var group = event.group;
    var target = event.target;
    var id = target.data.id;
    if (event.type == 'click'){
        if (group != 'nodes' && group != 'edges'){
            return;
        } else {
            
        }
    }
}

function layout(layout){
    var selectedValue = layout.selectedIndex;
    if(selectedValue == 0){
        return;
    }
    
    var layoutType = layout.options[selectedValue].text;
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
    cw_has_cache = true ;
    setTimeout(function(){
        initFilterCytoscapeweb();
    },1000);
}