(function() {
var blocked = false;
  // getElementById
  function $id(id) {
    return document.getElementById(id);
  }


  // file drag hover
  function FileDragHover(e) {
    e.stopPropagation();
    e.preventDefault();
    e.target.className = (e.type == "dragover" ? "hover" : "");
  }


  // file selection
  function FileSelectHandler(e) {

    // cancel event and hover styling
    FileDragHover(e);

    // fetch FileList object
    var files = e.target.files || e.dataTransfer.files;

    // process all File objects
      ParseFile(files[0]);

  }


  // output file information
  function ParseFile(file) {
    if (!blocked)
      if (file.type != "text/csv")
        alert("File must be of type CSV");
      else if (file.size > 1024000)
        alert("File must have max. 1 MB");
      else
      {
         var fd = new FormData();
         
         var mes = $id("filedrag");
         var token = $id("token");

         mes.innerHTML = "Loading...";
         blocked = true;
         fd.append("action", "my_action");
         fd.append("fileselect", file);
         fd.append("token", token.value);


         var xhr = new XMLHttpRequest();
         xhr.open("POST", ajaxurl, true);

         xhr.onload = function() {
            blocked = false;
            if (this.status == 200) {
              var resp = JSON.parse(this.response);
         
              console.log("Server got:", resp);
              if (resp.message)
                alert(resp.message);

              mes.innerHTML = resp.message;
         
            };
          };

         xhr.send(fd);

      
      }  

  }

  // initialize
  function Init() {

    var fileselect = $id("fileselect"),
      filedrag = $id("filedrag"),
      submitbutton = $id("submitbutton"),
      uploadfield = $id("fileselect");

    // file select
    fileselect.addEventListener("change", FileSelectHandler, false);

    // is XHR2 available?
    var xhr = new XMLHttpRequest();
    if (xhr.upload) {

      // file drop
      filedrag.addEventListener("dragover", FileDragHover, false);
      filedrag.addEventListener("dragleave", FileDragHover, false);
      filedrag.addEventListener("drop", FileSelectHandler, false);
      filedrag.style.display = "block";
      // remove submit button
      submitbutton.style.display =   "none";
      uploadfield.style.display = "none";
    }

  }

  // call initialization file
  if (window.File && window.FileList && window.FileReader) {
    Init();
  }


})();