jQuery(document).ready(function($) {
    //Read current domain. Base URL hardcoded.
    //let inputcheck = ['', 'eeSFL_FileNameNew']
    const domain = window.location.host;
    const currentURL = window.location.href;
    if (currentURL.includes("/de/")) {
        var baseurl = 'https://' + domain + '/de/start/intranet-2/';
    } else {
        var baseurl = 'https://' + domain + '/home/intranet/';
    }

    var pattern_count = 0;
    function updatePattern(element) {
        const currentName = $(element).attr('name');
        if (currentName === 'eeSFL_NewFolderName') {
            //$(element).attr('required', 'required');
            $(element).attr('pattern', '^[a-zA0-9_\\-]*$');
            $(element).attr('title', 'Only alphanumeric characters, as well as _ and - allowed');
        } else if (currentName === 'eeSFL_FileNameNew') {

        } else if (pattern_count > 0) {
            $(element).removeAttr('pattern');
            $(element).removeAttr('title');
        } else {}
    }

    if (currentURL.indexOf(baseurl) !== -1) {
        //Include links and copy link button.
        $('a').each(function() {
            var currentHref = $(this).attr('href');
            var currentCopyLink = $(this).attr('onclick');
            //Check if URL fits CDN or original domain name, split URL and use latter part to construct working URL w/o CDN.
            if (currentHref && currentHref.includes('/ee-get-file/') && currentHref.indexOf(baseurl) === -1) {
                var urlParts = currentHref.split('/ee-get-file/');
                var newHref = 'https://' + domain + '/ee-get-file/' + urlParts[1];
                $(this).attr('href', newHref);
            }
            if (currentCopyLink && currentCopyLink.includes('/ee-get-file/') && currentCopyLink.indexOf(baseurl) === -1) {
                var urlParts = currentCopyLink.split('/ee-get-file/');
                var newCopyLink = 'eeSFL_CopyLinkToClipboard(\'https://' + domain + '/ee-get-file/' +urlParts[1];
                $(this).attr('onclick', newCopyLink)
            }
        });
        
        //Insert pattern into relevant forms once on loading page.
        $('input[type="text"]').each(function() {
                updatePattern(this);
                pattern_count += 1;
        });

        //Observe change in FileOpsActions to remove and re-add pattern as needed.
        const targetElement = document.getElementById('eeSFL_FileOpsActionInput');
        const observer = new MutationObserver(function(mutationsList) {
            mutationsList.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'name') {
                    updatePattern(targetElement);
                }
            });
        });
        const observerConfig = { attributes: true };
        observer.observe(targetElement, observerConfig);

        //Start with unknown value. If pattern matches, do nothing, else reset the input value and return an error message.
        var previousValidValue = 'unknown';
        document.getElementById('eeSFL_FileNameNew').addEventListener('input', function() {
            var inputField = this.value;
            var pattern = /^[a-zA-Z0-9-_]+\.(avi|bmp|csv|doc|docm|docx|jpeg|jpg|mov|mp3|mp4|msg|odp|ods|odt|pdf|png|ppt|pptm|pptx|svg|tif|tiff|tsv|txt|vsd|xls|xlsm|xlsx|zip)$/;
            var pattern2 = /^[a-zA-Z0-9-_]*$/;
            
            if (!pattern.test(inputField) && !pattern2.test(inputField)) {
                alert('Invalid input. Only alphanumeric characters, as well as \'_\' and \'-\' allowed. Use "File Nice Name" for complex names.');
                if (previousValidValue == 'unknown') {
                    previousValidValue = document.querySelector('#eeSFL_Modal_EditFile > div:nth-child(2) > p:nth-child(3)').innerHTML;
                    this.value = previousValidValue;
                } else {
                    this.value = previousValidValue;
                }
            } else {
                previousValidValue = inputField;
            }
        });
        function resetValidValue() {
            previousValidValue = 'unknown'
        }

        //Reset previously saved state to 'unknown'. When Modal Form is closed.
        const modal_button1 = document.querySelector('.button[onclick="eeSFL_FileEditSaved()"]');
        const modal_button2 = document.querySelector('.eeSFL_ModalClose');
        if (modal_button1 && modal_button2) {
            modal_button1.addEventListener('click', resetValidValue);
            modal_button2.addEventListener('click', resetValidValue);
        } else {
            //console.log("Not found");
        }
    }
});

{
move_ui_running_dir = {};
move_ui_lvl = 0;
move_ui_empty_dir = 0;
move_ui_nav_bar = [];

jQuery(document).ready(function($){
  //Listen for click on Move button. Always resets to Main Folder on click.
  $('#eeSFL_Modal_MoveFile').find('.eeSFL_ModalBody').append('<div class=\"eeSFL_Move_UI_Nav\"></div><div class=\"eeSFL_Move_UI_Msgs_warn\"></div><div class=\"eeSFL_Move_UI_Msgs\"></div><div id="eeSFL_Move_UI_ID" class="eeSFL_Move_UI_Class"></div>')
  const regex = /eeSFL_OpenMoveFileModal\((\d+\))/
  const buttons = document.querySelectorAll('a[href="#"]')
  buttons.forEach( button => {
    const onclick = button.getAttribute('onclick')
    if (onclick != null) {
      const match = onclick.match(regex)
      if (match) {
	//const onclick_int = parseInt(match[1], 10)
	//button.addEventListener('click', function() { initialize_move_ui(onclick_int) })
	button.addEventListener('click', function() { initialize_move_ui() })
    	}
    };
  });
});

function update_move_ui_nav_bar(lvl, lower_bound, upper_bound, name) {
  let new_entry = {
    'level' : lvl,
    'lower_bound' : lower_bound,
    'upper_bound' : upper_bound,
    'name' : name
  }
  //console.log(new_entry)
  move_ui_nav_bar.push(new_entry)
  $('.eeSFL_Move_UI_Nav').empty()
  move_ui_nav_bar.forEach( function (obj) {
    var btn_name = obj.name
    $('.eeSFL_Move_UI_Nav').append("<button id=\"" + String(btn_name) + String(obj.lvl) + "_nav_btn\">📁 " + btn_name + "</button>")
    document.getElementById(String(btn_name) + String(obj.lvl) + "_nav_btn").addEventListener( "click", function() { nav_bar_move(obj.lower_bound, obj.upper_bound, obj.level, obj.name) } )
  });
};
function nav_bar_move(lower_bound, upper_bound, lvl, dir_name) {
  select = document.getElementById('eeSFL_Destination')
  if ( move_ui_lvl > lvl ) {	
    lvl_diff = parseInt(move_ui_lvl) - parseInt(lvl)
    move_ui_lvl = lvl
    move_ui_running_dir = traverse_dir(lower_bound, upper_bound, move_ui_lvl)
    if ( eeSFL_SubFolder != '/' && dir_name == 'Home') {
    move_ui_running_dir[ eeSFL_Destinations.length + 1 ] = 'Main Folder' 
    };
    update_dir(move_ui_running_dir)
    move_ui_nav_bar.splice(-lvl_diff-1)
    update_move_ui_nav_bar(move_ui_lvl, lower_bound, upper_bound, dir_name)	
    if ( eeSFL_SubFolder == '/' && dir_name == 'Home' ) {
      select.value = ""
    } else if ( eeSFL_SubFolder != '/' && dir_name == 'Home' ) {
      select.value = "/"
    } else {
    warn_copy(eeSFL_Destinations[lower_bound])
    }
  } else if ( move_ui_lvl == lvl && dir_name == 'Home' && eeSFL_SubFolder == '/' ) {
      select.value = ""
  } else if ( move_ui_lvl == lvl && dir_name == 'Home' && eeSFL_SubFolder != '/' ) {
      select.value = "/"
  } else if ( move_ui_lvl == lvl ) {
      warn_copy(eeSFL_Destinations[lower_bound])
  } else {
  };
};

function initialize_move_ui() {
  $('.eeSFL_Move_UI_Msgs_warn').empty()
  move_ui_lvl = 0
  var upper_bound = eeSFL_Destinations.length
  var top_level_dir = traverse_dir(0, upper_bound, move_ui_lvl)
  if ( eeSFL_SubFolder != '/' ) {
    top_level_dir[ eeSFL_Destinations.length + 1 ] = 'Main Folder'
};
  move_ui_running_dir = top_level_dir
  update_dir(top_level_dir)
  move_ui_nav_bar.length = 0
  update_move_ui_nav_bar(move_ui_lvl, 0, upper_bound, 'Home')
};

//Update directories. Traverses eeSFL_Destinations. Assumes linear hierarchy for all directories within eeSFL_Destinations.
function traverse_dir(lower_bound, upper_bound, move_ui_lvl) {
  var temp_dir = {}
  //console.log("Lower: "+lower_bound+" Upper: "+upper_bound+" Level: "+move_ui_lvl) 
  if ( upper_bound == undefined || upper_bound > eeSFL_Destinations.length ) {
    upper_bound = eeSFL_Destinations.length
  }
  while ( parseInt(lower_bound) < parseInt(upper_bound) ) {
    /*Previously introduced check to avoid navigation for directories that can't be copied into. 
      Disabled due to inconsistent behaviour.
    //if ( dir_int != lower_bound ) {*/
    var working_dir = eeSFL_Destinations[lower_bound].split('/')
    if ( working_dir.length == move_ui_lvl + 2 ) {
        temp_dir[lower_bound] = working_dir[move_ui_lvl]
      };
    //};
    lower_bound++
  };
  if ( Object.keys(temp_dir).length == 0 ) {
    move_ui_empty_dir = 1
    return move_ui_running_dir
  } else {
    move_ui_empty_dir = 0
    return temp_dir
  };
};

/* One-liner for extracting ID.
Courtesy of https://stackoverflow.com/questions/6268679/how-to-get-the-key-of-a-key-value-javascript-object */
function get_key(obj, value) {
  return Object.keys(obj)[Object.values(obj).indexOf(value)]
};

//Insert HTML code for all directories. 
function update_dir(input_dir) {
  if ( Object.keys(input_dir).length > 0 && move_ui_empty_dir == 0) {
    $('.eeSFL_Move_UI_Class').empty()
    $('.eeSFL_Move_UI_Msgs').empty()
    for ( const item in input_dir ) {
      const item_string = input_dir[item]
      const item_id = get_key(input_dir, item_string)
      $('.eeSFL_Move_UI_Class').append("<button class=\"Modal_Dir\" id=\"Move_UI_Btn_" + String(item_id) + "\"><img src=\"https://bbmri.at/wp-content/plugins/ee-simple-file-list-pro/images/thumbnails/folder.svg\" width=\"32\" height=\"32\"><br>" + item_string + "</button>")
      document.getElementById("Move_UI_Btn_" + String(item_id)).addEventListener( "click", function() { change_dir(item_id) } )
    }
  } else {
    move_ui_lvl--
    $('.eeSFL_Move_UI_Msgs').empty()
    $('.eeSFL_Move_UI_Msgs').prepend("<p>No subfolders present in directory</p><br>")
    //move_ui_nav_bar.splice(-1)
  }
};

//Update variables for directory change. Keep track of traversal level. 
function change_dir(dir_id) {
  //Update dropdown menu
  var dir_dest = eeSFL_Destinations[dir_id]
  select = document.getElementById('eeSFL_Destination')
  if ( dir_id == parseInt(eeSFL_Destinations.length) + 1 ) {
    select.value = "/"
  } else {
    warn_copy(dir_dest)
    //select.value = dir_dest
    //Set upper and lower bound for directory traversal. Do so by sorting them into array, and counting up from there. Assumes the order stays the same, between arrays. If only a single dir exists or it is the last in the array, set upper bound to next dir.
    dir_int = parseInt(get_key(Object.keys(move_ui_running_dir),String(dir_id))) + 1
    if ( Object.keys(move_ui_running_dir).length == dir_int ) {
      var dir_name = dir_dest.split('/')[move_ui_lvl]
      var next_id = parseInt(dir_id) + 1
      while ( next_id < eeSFL_Destinations.length && eeSFL_Destinations[next_id].split('/')[move_ui_lvl] == dir_name ) {
        next_id++
      }
    } else {
    var next_id = Object.keys(move_ui_running_dir)[dir_int]
    }
    move_ui_lvl++
    move_ui_running_dir = traverse_dir(dir_id, next_id, move_ui_lvl)
    update_dir(move_ui_running_dir)
    if ( move_ui_lvl > 0 && Object.keys(move_ui_running_dir).length > 0 && move_ui_empty_dir == 0) {
	var dir_name_new = eeSFL_Destinations[dir_id].split('/')[parseInt(move_ui_lvl)-1]
	update_move_ui_nav_bar(move_ui_lvl, parseInt(dir_id), parseInt(next_id), dir_name_new)
    } 
  }
};

/* Courtesy of https://stackoverflow.com/questions/9790845/check-if-an-option-exist-in-select-element-without-jquery
Check if dir exists in dropdown menu & user if not. */ 
function warn_copy(dir_value) {
    select = document.getElementById('eeSFL_Destination')
    for ( var i = 0, l = select.options.length; i < l; i++ ) {
      if ( select.options[i].value == dir_value ) {
        $('.eeSFL_Move_UI_Msgs_warn').empty()
        select.value = dir_value
        return
      } else {
	select.value = ""
        $('.eeSFL_Move_UI_Msgs_warn').empty()
        $('.eeSFL_Move_UI_Msgs_warn').prepend("<p>Unable to move to folder. Please choose another.</p><br>")
      }
}};
};
