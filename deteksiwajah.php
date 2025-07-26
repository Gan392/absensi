<script>
  var waitingDialog = waitingDialog || (function($) {
    var $dialog = $(
      '<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top:15%; overflow-y:visible;">' +
      '<div class="modal-dialog modal-m">' +
      '<div class="modal-content">' +
      '<div class="modal-header"><h3 style="margin:0;"></h3></div>' +
      '<div class="modal-body">' +
      '<img width="100%" src="https://i.pinimg.com/originals/12/e8/a6/12e8a6a547e317524121f7a5d6084036.gif">' +
      '</div>' +
      '</div></div></div>');

    return {
      show: function(message, options) {
        if (typeof options === 'undefined') {
          options = {};
        }
        if (typeof message === 'undefined') {
          message = 'Loading';
        }
        var settings = $.extend({
          dialogSize: 'm',
          progressType: '',
          onHide: null
        }, options);
        $dialog.find('.modal-dialog').attr('class', 'modal-dialog').addClass('modal-' + settings.dialogSize);
        $dialog.find('.progress-bar').attr('class', 'progress-bar');
        if (settings.progressType) {
          $dialog.find('.progress-bar').addClass('progress-bar-' + settings.progressType);
        }
        $dialog.find('h3').text(message);
        if (typeof settings.onHide === 'function') {
          $dialog.off('hidden.bs.modal').on('hidden.bs.modal', function(e) {
            settings.onHide.call($dialog);
          });
        }
        $dialog.modal();
      },
      hide: function() {
        $dialog.modal('hide');
      }
    };

  })(jQuery);
</script>


<script>
  //----------------------------GLOBAL VARIABLE FOR FACE MATCHER------------------------------------
  var faceMatcher = undefined
  //----------------------------------------------------------------------------------------------

  waitingDialog.show('Loading....', {
    dialogSize: 'sm',
    progressType: 'success'
  })
  $("#parent1").hide();
  $("#parent2").hide();
  Promise.all([
    faceapi.nets.faceRecognitionNet.loadFromUri('<?php echo $lokasi_request; ?>models'),
    faceapi.nets.faceLandmark68Net.loadFromUri('<?php echo $lokasi_request; ?>models'),
    faceapi.nets.ssdMobilenetv1.loadFromUri('<?php echo $lokasi_request; ?>models'),
    faceapi.nets.tinyFaceDetector.loadFromUri('<?php echo $lokasi_request; ?>models')
  ]).then(start)

  async function start() {
    $.ajax({
      datatype: 'json',
      url: "<?php echo $lokasi; ?>fetch.php",
      data: ""
    }).done(async function(data) {
      if (data.length > 2) {
        var json_str = "{\"parent\":" + data + "}"
        content = JSON.parse(json_str)
        for (var x = 0; x < Object.keys(content.parent).length; x++) {
          for (var y = 0; y < Object.keys(content.parent[x]._descriptors).length; y++) {
            var results = Object.values(content.parent[x]._descriptors[y])
            content.parent[x]._descriptors[y] = new Float32Array(results)
          }
        }
        faceMatcher = await createFaceMatcher(content);
      }
      waitingDialog.hide()
      $('#parent1').show()
      $('#parent2').show()
      run();
    });
  }

  // Create Face Matcher
  async function createFaceMatcher(data) {
    const labeledFaceDescriptors = await Promise.all(data.parent.map(className => {
      const descriptors = [];
      for (var i = 0; i < className._descriptors.length; i++) {
        descriptors.push(className._descriptors[i]);
      }
      return new faceapi.LabeledFaceDescriptors(className._label, descriptors);
    }))
    return new faceapi.FaceMatcher(labeledFaceDescriptors, 0.6);
  }


  async function onPlay() {
    const videoEl = $('#vidDisplay').get(0)
    if (videoEl.paused || videoEl.ended)
      return setTimeout(() => onPlay())

    $("#overlay").show()
    const canvas = $('#overlay').get(0)

    if ($("#register").hasClass('active')) {
      const options = getFaceDetectorOptions()
      const result = await faceapi.detectSingleFace(videoEl, options)
      if (result) {
        const dims = faceapi.matchDimensions(canvas, videoEl, true)
        dims.height = 500
        dims.width = 700
        canvas.height = 500
        canvas.width = 700
        const resizedResult = faceapi.resizeResults(result, dims)
        faceapi.draw.drawDetections(canvas, resizedResult)
      } else {
        $("#overlay").hide()
      }
    }

    if ($("#login").hasClass('active')) {
      if (faceMatcher != undefined) {
        //--------------------------FACE RECOGNIZE------------------
        const input = document.getElementById('vidDisplay')
        const displaySize = {
          width: 700,
          height: 500
        }
        faceapi.matchDimensions(canvas, displaySize)
        const detections = await faceapi.detectAllFaces(input).withFaceLandmarks().withFaceDescriptors()
        const resizedDetections = faceapi.resizeResults(detections, displaySize)
        const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor))
        results.forEach((result, i) => {
          const box = resizedDetections[i].detection.box
          const drawBox = new faceapi.draw.DrawBox(box, {
            label: result.toString()
          })
          drawBox.draw(canvas)
          var str = result.toString()
          rating = parseFloat(str.substring(str.indexOf('(') + 1, str.indexOf(')')))
          str = str.substring(0, str.indexOf('('))
          str = str.substring(0, str.length - 1)




          match = true;


          var lat, lng, id_matapelajaran;
          lat = document.getElementById("lat").value;
          lng = document.getElementById("lng").value;
          id_matapelajaran = document.getElementById("id_matapelajaran").value;



          // $.ajax({
          //   type: 'GET',
          //     url: "absen.php?proses="+str+"&lat="+lat+"&lng="+lng,
          //     data: ""
          // }).done(async function(data) {
          //   console.log(str);
          // });

          $.ajax({
            url: "absen.php?proses=" + str + "&lat=" + lat + "&lng=" + lng + "&id_matapelajaran=" + id_matapelajaran,
            success: function(result) {
              if (result == "JAUH") {
                window.location.href = "jarak.php?lat=" + lat + "&lng=" + lng;
              }


            }
          });



          $("#siapa").html("... " + str + " ...<br>Berhasil Presensi")
          $("#prof_img").attr('src', "<?php echo $lokasi; ?>data/" + str + "/image0.png")



        })
        //---------------------------------------------------------------------  
      } else {
        $("#prof_img").attr('src', "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTJmBPU-lFArZe9hfYDS6KxJR3max-uXwJdA&usqp=CAU")

      }
    }

    setTimeout(() => onPlay())
  }

  async function run() {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: {}
    })
    const videoEl = $('#vidDisplay').get(0)
    videoEl.srcObject = stream
  }

  // tiny_face_detector options
  let inputSize = 160
  let scoreThreshold = 0.4

  function getFaceDetectorOptions() {
    return new faceapi.TinyFaceDetectorOptions({
      inputSize,
      scoreThreshold
    });
  }

  async function load_neural() {
    waitingDialog.show('Initializing neural data....', {
      dialogSize: 'sm',
      progressType: 'success'
    })
    $.ajax({
      datatype: 'json',
      url: "<?php echo $lokasi; ?>fetch.php",
      data: ""
    }).done(async function(data) {
      if (data.length > 2) {
        var json_str = "{\"parent\":" + data + "}"
        content = JSON.parse(json_str)
        console.log(content)
        for (var x = 0; x < Object.keys(content.parent).length; x++) {
          for (var y = 0; y < Object.keys(content.parent[x]._descriptors).length; y++) {
            var results = Object.values(content.parent[x]._descriptors[y]);
            content.parent[x]._descriptors[y] = new Float32Array(results);
          }
        }
        faceMatcher = await createFaceMatcher(content);
      }
      waitingDialog.hide()
    });
  }
</script>

<script>
  $(document).ready(async function() {

    var counter = 5;
    const descriptions = [];

    // -------------Initialize---------------
    //$("#login").css('background-color','yellow');
    $("#login").addClass('active');
    //$("#register").css('background-color','white');
    $("#register").removeClass('active');

    if ($("#login").hasClass('active')) {
      $("#reg_disp").hide();
      $("#log_disp").show();
    } else if ($("#register").hasClass('active')) {
      $("#reg_disp").show();
      $("#log_disp").hide();
    }

    //---------------------------------------


    $("#login").click(function() {
      $.ajax({
        datatype: 'json',
        url: "<?php echo $lokasi; ?>fetch.php",
        data: ""
      }).done(function(data) {
        labeled = JSON.parse(data)
      });
      //$(this).css('background-color','yellow')
      //$("#register").css('background-color','white')
      $(this).addClass('active')
      $("#register").removeClass('active')
      $("#reg_disp").hide()
      $("#log_disp").show()
      $("#prof_img").removeAttr('src')
      // $("#fname").val('')
      // $("#fname").val('')
      $("#logname").html('')
      $("#fname").prop("readonly", false)
      $("#fname").prop("readonly", false)
      counter = 5
      description = []
      $("#tries").html("Jumlah : " + counter)
    });

    $("#register").click(function() {
      //$(this).css('background-color','yellow')
      //$("#login").css('background-color','white')
      $(this).addClass('active')
      $("#login").removeClass('active')
      $("#reg_disp").show()
      $("#log_disp").hide()
      $("#prof_img").removeAttr('src')
      // $("#fname").val('')
      // $("#fname").val('')
      $("#logname").html('')
      $("#fname").prop("readonly", false)
      $("#fname").prop("readonly", false)
      counter = 5
      description = []
      $("#tries").html("Jumlah Pengujian : " + counter)



    });



    $("#tries").html("Jumlah Pengujian : " + counter)

    $("#capture").click(async function() {
      var data = $("#fname").val();
      const label = data;

      // if($("#fname").hasClass('active') && $("#fname").hasClass('active') && $("#fname").val() && $("#fname").val()){
      if ($("#register").hasClass('active')) {

        $("#fname").prop("readonly", true)
        $("#fname").prop("readonly", true)
        if (counter <= 5 && counter >= 0) {
          var canvas = document.createElement('canvas');
          var context = canvas.getContext('2d');
          var video = document.getElementById('vidDisplay');
          context.drawImage(video, 0, 0, 600, 350);
          var capURL = canvas.toDataURL('image/png');
          var canvas2 = document.createElement('canvas');
          canvas2.width = 700;
          canvas2.height = 500;
          var ctx = canvas2.getContext('2d');
          ctx.drawImage(video, 0, 0, 700, 500);
          var new_image_url = canvas2.toDataURL();
          var img = document.createElement('img');
          img.src = new_image_url;
          document.getElementById("prof_img").src = img.src;

          const detections = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
          if (detections != null) {
            descriptions.push(detections.descriptor);
            var descrip = descriptions;
            counter--;
            $("#tries").html("Jumlah Pengujian : " + counter)
            if (counter == 0) {
              // Save Image
              $.ajax({
                type: "POST",
                url: "<?php echo $lokasi; ?>ajax.php",
                data: {
                  image: img.src,
                  path: data
                }
              }).done(function(o) {
                console.log('Image Saved');
              });


              waitingDialog.show('Loading.............', {
                dialogSize: 'sm',
                progressType: 'success'
              })
              var postData = new faceapi.LabeledFaceDescriptors(label, descrip);
              $.ajax({
                  url: 'json.php',
                  type: 'POST',
                  data: {
                    myData: JSON.stringify(postData)
                  },
                  datatype: 'json'
                })
                .done(async function(data) {
                  load_neural()
                  alert("Proses Selesai..")
                  console.log("Success!")
                  waitingDialog.hide()
                  counter = 5
                  $("#tries").html("Jumlah : " + counter)
                  $("#fname").val('')
                  $("#fname").val('')
                  $("#prof_img").removeAttr('src')
                  $("#fname").prop("readonly", false)
                  $("#fname").prop("readonly", false)
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                  alert("Gagal Proses, Ulangi Kembali");
                });
              const descriptions = [];
            }
          } else {
            alert("Tidak Mendeteksi Wajah");
          }
        } else {
          alert("Selesai");
          counter = 5;
          const descriptions = [];
        }


      }
      // }
      // else{
      //   if(!$("#fname").val() || !$("#fname").hasClass('active')){
      //     $("#fname").css('border','1px solid red');
      //     $("#fname").removeClass('active')      
      //   }
      //   if(!$("#fname").val() || !$("#fname").hasClass('active')){
      //     $("#fname").css('border','1px solid red');
      //     $("#fname").removeClass('active')      
      //   }
      // }
    });

    var format = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;

    $("#fname").keyup(function() {
      var str = $(this).val().toUpperCase();
      $(this).val(str);
      if (format.test(str) && str == "") {
        $(this).css('border', '1px solid red');
        $(this).removeClass('active')
      } else {
        $(this).css('border', '3px solid black');
        $(this).addClass('active')
      }
    });

    $("#fname").keyup(function() {
      var str = $(this).val().toUpperCase();
      $(this).val(str);
      if (format.test(str) || str == "") {
        $(this).css('border', '1px solid red');
        $(this).removeClass('active')
      } else {
        $(this).css('border', '3px solid black')
        $(this).addClass('active')
      }
    });
  });
</script>