var path = "";
var library_req = 1;


function library_refresh() {
    command("window.library_req",'lsinfo "'+path+'"',library_refresh_handler);
}


function library_refresh_handler() {
    //alert(library_req.readyState);
    if ((library_req.readyState == 4) && (library_req.status == 200)) {
        //document.getElementById('status').innerHTML = library_req.responseText;
        
        var list_raw = library_req.responseText.split("\n");
        //alert(library_req.responseText);
        //alert(list_raw.length);
        var ind = -1;
        var list = new Array();
        
        for (i = 0; i < list_raw.length; i++) {
            
            var data = list_raw[i].split(': ');
            //alert(data[0]);
            if ((data[0] == 'file') || (data[0] == 'directory') || (data[0] == 'playlist')) {
                
                ind += 1;

                
            
                list[ind] = new Array();
                list[ind]['type'] = data[0];
                
            
                list[ind]['file'] = data.slice(1).join('');
            }
            else {
                //alert(data);
                list[ind][data[0]] = data.slice(1).join('');
                
            }

        }

        //alert(list.length);
        //alert(list[0]);

        var dir_node = document.getElementById('dir');

        if (dir_node) {
            items = dir_node.childNodes;
            // clear old rows
            for(i = items.length-1; i >= 0; i--) {
                dir_node.removeChild(items[i]);
            }
            
            // create new rows
            for(i = 0; i < list.length; i++) {
                if (list[i]['type'] == 'playlist') {
                    continue;
                }

                var tr = document.createElement('tr');
                if (i % 2) {
                    tr.className = 'row_dark';
                }
                else {
                    tr.className = 'row_light';
                }
                //tr.setAttribute("id","song_"+playlist[i]['Id']);
                
                dir_node.appendChild(tr);
                
                var td = document.createElement('td');
                tr.appendChild(td);
                td.innerHTML = "[<a href=\"javascript:library_add('" + addslashes(list[i]['file']) + "')\">add</a>]";
                //alert(list.length + ' '+i);
                //alert('i: '+list[i]);

                if (list[i]['type'] == 'directory') {
                
                    var td = document.createElement('td');
                    tr.appendChild(td);
                

                    td.innerHTML = "[<a href=\"javascript:library_update('" + addslashes(list[i]['file']) + "')\">upd</a>]";
                
                
                    var td = document.createElement('td');
                    tr.appendChild(td);

                    td.setAttribute('colspan',2);
                    var a = document.createElement('a');
                    td.appendChild(a);
                    
                    a.setAttribute('href',"javascript:library_chdir('"+addslashes(list[i]['file'])+"/');");
                    a.innerHTML = list[i]['file'].slice(path.length);

                }
                else {
                    var td = document.createElement('td');
                    tr.appendChild(td);
                

                    td.innerHTML = "";
                    var td = document.createElement('td');
                    tr.appendChild(td);
                
                    
                    td.innerHTML = make_title(list[i]);
                
                
                    //td.onclick = new Function("selection_start(this.parentNode)");


                    var td = document.createElement('td');
                    tr.appendChild(td);
                    td.setAttribute('align','right');
                    var a = document.createElement('a');
                    td.appendChild(a);
                    
                    //a.setAttribute('href',"javascript:command("window.library_req",'playid " + list[i]['Id'] + "',fake);");
                    //alert(list[i]);
                    a.innerHTML = make_time(list[i]['Time']);
                }

            }
            
            
        }

        var subpath = path.split('/');
        subpath.pop();

        document.title = 'phpMp3::"'+subpath.join('/') + '"';

        var crumb_node = document.getElementById('crumb');

        if (crumb_node) {

            //alert(subpath.join('-'));
            var crumb = new Array("<a href=\"javascript:library_chdir('');\">Root</a>");
            
            for (i = 0; i < subpath.length; i++) {
                if (i == subpath.length-1) {
                    crumb.push("<span>" + subpath[i]  + "</span>");
                }
                else {
                    crumb.push("<a href=\"javascript:library_chdir('" + addslashes(subpath.slice(0,i+1).join('/')) + "/');\">"+subpath[i]+"</a>");
                
                }
                
            }

            crumb_node.innerHTML = crumb.join('&nbsp;/&nbsp;');
        }


    }

}


function library_chdir(path_new) {

    path = path_new;
    library_refresh();

}



function library_add(s) {

    command("window.library_req",'add "' + s + '"',fake);
    
}

function library_update(s) {

    command("window.library_req",'update "' + s + '"',fake);

}

