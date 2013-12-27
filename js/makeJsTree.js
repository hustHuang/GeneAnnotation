var _selectedNodes = [];
var _treeSelectedNames = {};
var _jTreeUrl = './ajax/JSTree.ajax.php';
function loadTreeView(id, search_word, ontology) {
    var title, titleId;
    if (ontology === 'c') {
        title = 'CRISP Thesaurus';
        titleId = '1140093';
    } else {
        title = 'SNOMED Clinical Terms';
        titleId = '2720507';
    }
    $.ajax({
        type: 'POST',
        url: _jTreeUrl,
        dataType: "JSON",
        data: {
            'search_word': search_word,
            'chosen': ontology
        },
        async: false,
        success: function(initdata) {
            //alert(initdata.initially_open);
            //$('#'+id+' .loading').hide();
            if ($('#dttree').length === 0) {
                var termTreeHtml = '<div class="treeview" id="dttree"></div>';
                $('#' + id).prepend(termTreeHtml);
            }
            $("#dttree").jstree({
                "themes": {
                    "theme": "default",
                    "dots": false,
                    "icons": false
                },
                "json_data": {
                    "data": [{
                            "attr": {
                                "id": "li_" + titleId
                            },
                            "data": {
                                "title": title,
                                "attr": {
                                    "id": titleId,
                                    "href": "javascript:void(0);",
                                    "class": "treenode"
                                }
                            },
                            "state": "closed"
                        }],
                    "ajax": {
                        "url": "./ajax/TreeNode.ajax.php",
                        "data": function(n) {
                            return {
                                term_id: n.attr("id") == null ? "0" : n.attr("id").split('_')[1]
                               ,chosen: ontology
                            };
                        }
                    },
                    "progressive_render": true
                },
                "plugins": ["themes", "json_data", "contextmenu"],
                "core": {
                    "initially_load": initdata.initially_load,
                    "initially_open": initdata.initially_open
                }
            }).bind("open_node.jstree", function(event, data) {
                if ((data.inst._get_parent(data.rslt.obj)).length) {
                    data.inst.open_node(data.inst._get_parent(data.rslt.obj), false, true);
                }
            }).bind('after_open.jstree', function(e, data) {
                var count = _selectedNodes.length;
                for (var i = 0; i < count; i++) {
                    if (!$('#' + id + ' li#' + _selectedNodes[i]).hasClass('selected')) {
                         $('#' + id + ' li#' + _selectedNodes[i]).addClass('selected');
                    }
                }
            });
            var count = initdata.queried_ids.length;
            for (var i = 0; i < count; i++) {
                _selectedNodes[i] = initdata.queried_ids[i];
            }
        }
    });

   setTimeout(function() {
        $.each(_selectedNodes, function(i, e) {
            var id = $.trim(e.split("_")[1]);
            var name = $('#' + id).text();
            _treeSelectedNames[id] =$.trim(name);
        });
    }, 10000);
}

function searchNode() {
    var stype = $("#sl").val();
    var searchWord = '';
    for (var key in _treeSelectedNames) {
        searchWord += _treeSelectedNames[key] + ',';
    }
    $(".loading").show();
    $("#network_container").hide();
    $.ajax({
        type: 'POST',
        url: './ajax_search.php',
        dataType: 'JSON',
        data: {terms: searchWord, chosen: stype},
        async: false,
        success: function(data) {
            $(".loading").hide();
            $("#network_container").show();
            $('#network_container').empty();
            makeCytoscapeWebView('network_container', eval('(' + data.cw_node_data + ')'), eval('(' + data.cw_edge_data + ')'));
        },
        error: function(a, b, c) {
            alert(a);
            alert(b);
            alert(c);
        }
    });
}

function trimString(str) {
    return str.replace(/(^\s*)|(\s*$)/g, "");
}
