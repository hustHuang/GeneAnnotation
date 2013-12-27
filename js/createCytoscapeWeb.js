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
    attrName: "ngc",                            // nodeGroupCode
    entries: [
    {
        attrValue: "q",
        value: "#FF9086"
    },      // disease nodes

    {
        attrValue: "r",
        value: "#ffffff"
    }       // drugs nodes
    ]
};

//Mapping network groups to edge colors:
var edgeColorMapper = {
    attrName: "egc",                                // edgeGroupCode
    entries: [
    {
        attrValue: "c",
        value: "#c3844c"
    },          //Correlation

    {
        attrValue: "ai",
        value: "#2fb56d"
    },         //Interaction

    {
        attrValue: "pai", 
        value: "green"
    },	    //Positive Interaction

    {
        attrValue: "nai", 
        value: "red"
    },	    //Negative Interaction

    {
        attrValue: "coexp",
        value: "#FBD10A"
    },      //Co-expression

    {
        attrValue: "coloc",
        value: "#6261fc"
    },      //Co-localization

    {
        attrValue: "pi",
        value: "#9EB5E6"
    },         //Physical interactions

    {
        attrValue: "spd",
        value: "#00CCFF"
    },        //Shared protein domains
    ]
};

var colorMapper = { attrName: "distance",  minValue: "#ffff00", maxValue: "#00ff00" };

var cw_style = {
    global: {
        backgroundColor:"#FFF"//,
        //tooltipDelay: 2000
    },
    nodes : {
        shape: "ELIPSE",
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
        //labelGlowColor:"0000cd",
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
            defaultValue: "#FBD10A"
            //discreteMapper: colorMapper
        },
        width:{
            defaultValue: 3
           ,continuousMapper: {
                attrName: "score",
                minValue: 3,
                maxValue: 6
            }
        }
        /*,
         label: {
            passthroughMapper: {
               // attrName: "pvalue"
            },
            labelFontSize: 15,
            labelFontWeight: "bold",
            labelGlowColor: "#ffffff"
           
        }
       */
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
                type: "string"
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
               // weightAttr : "count"
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