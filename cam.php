<div class="row">
    <center>
        <div id='area_take' name='area_take'>
            <button id="take">Tirar Foto</button>&nbsp;<button id="cancel" onclick="populate('faces.php')">Cancelar</button><br>
            <video id="v"></video>
        </div>
        <div id='area_save' name='area_save' style='display: none;'>
            <button id="again">Tentar novamente</button>&nbsp;<button id="continue" onclick="next();">Continuar</button><br>
            <canvas width="360" height="480" id="canvas" style="display:none;"></canvas>
            <img src="" id="photo">
        </div>
    </center>
</div>

<form name="form1" id="form1" method="POST" action="?page=cadFaces.php">
    <input type="hidden" id="image" name="image" value="">
</form>

<script>
        function userMedia(){
            return navigator.getUserMedia = navigator.getUserMedia ||
            navigator.webkitGetUserMedia ||
            navigator.mozGetUserMedia ||
            navigator.msGetUserMedia || null;
        }

        if( userMedia() ){
            var videoPlaying = false;
            var constraints = {
                video: { width: 640, height: 480, facingMode: "user" },
                audio:false
            };
            var video = document.getElementById('v');

            var media = navigator.getUserMedia(constraints, function(stream){

                // URL Object is different in WebKit
                var url = window.URL || window.webkitURL;

                // create the url and set the source of the video element
                video.src = url ? url.createObjectURL(stream) : stream;

                video.play();
                videoPlaying  = true;
            }, function(error){
                console.log("ERROR");
                console.log(error);
            });


            document.getElementById('take').addEventListener('click', function(){
                if (videoPlaying){

                    $('#area_take').hide();
                    $('#area_save').show();

                    var canvas = document.getElementById('canvas');

                    var sourceX = 140;
                    var sourceY = 0;
                    var sourceWidth = 360;
                    var sourceHeight = 480;
                    var destWidth = sourceWidth;
                    var destHeight = sourceHeight;
                    var destX = 0;
                    var destY = 0;

                    canvas.getContext('2d').drawImage(video, sourceX, sourceY, sourceWidth, sourceHeight, destX, destY, destWidth, destHeight);
                    var data = canvas.toDataURL('image/jpeg');
                    document.getElementById('photo').setAttribute('src', data);
                }
            }, false);

            document.getElementById('again').addEventListener('click', function(){
                if (videoPlaying){
                    $('#area_save').hide();
                    $('#area_take').show();
                }
            }, false);

        } else {
            console.log("KO");
        };

function next ()
{
    var image = $('#photo').attr('src');
    $('#image').val(image);

    //alert $('#image').val();
    $('#form1').submit();
}

</script>
</body>
</html>
