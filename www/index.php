<html>
<head>
  <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css"/>
  <!--script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script-->
  <script   src="https://code.jquery.com/jquery-2.2.4.min.js"   integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="   crossorigin="anonymous"></script>
  <script src="/js/pixi.js"></script>
  <script src="/js/pixi-display.js"></script>
  <script src="/js/bootstrap.min.js"></script>
  <style>
    body, html {
      background-color: #000;
      overflow: none;
      margin: 0px;
    }
    #controls {
      color: #eee;
      position: absolute;
      right: 0px;
      width: 200px;
      top: 0px;
      padding: 10px;
    }
    #controls h1, #controls h2 {
      padding: 0px;
      margin: 0px;
      color: #fff;
    }
    #portrait_info {
      max-height: 70%;
      overflow-y: scroll;
    }
    #portrait_info img {
      margin: 0px 10px 10px 0px;
    }
    #controls h2 {
      color: #c00;
      margin: 0px 0px 0px 20px;
    }
    #controls button {
      width: 150px;
      height: 30px;
      margin: 5px;
    }
    #modal {
      position: absolute;
      top: 0px;
      left: 0px;
      width: 100%;
      height: 100%;
      background-color: #000;
    }
  </style>
</head>
<body>
<script>
  var pulse = 0;
  var base_scale = 0.5;
  var hover_scale = 2;
  var renderWidth = $(window).width() * 0.75;
  var renderHeight = $(window).height();
  var renderMiddle = { x: renderWidth / 2, y: renderHeight / 2 };
  var renderer = PIXI.autoDetectRenderer($(window).width(), $(window).height(), {backgroundColor : 0x000000});
  document.body.appendChild(renderer.view);

  // create the root of the scene graph
  var stage = new PIXI.Container();
  stage.displayList = new PIXI.DisplayList();
  
  // create a texture from an image path
  var portraits = [];
  /*for (var i = 0; i < texturenames.length; ++i)
  {
    console.log(i);
    PIXI.loader.add(texturenames[i], texturenames[i] + '.jpg');
  }*/
  
  var features;
  
  $.getJSON("/data/features.json", function(json) {
    features = json;
    sfeatures = []
    for (var i = 0; i < features.length; ++i)
      sfeatures.push(i);
    PIXI.loader
      .add('spritesheet', '/data/images/sprites.json')
      .load(init);
  });
  
  var spacingx = renderWidth / 51;
  var spacingy = renderHeight / 51;
  stage.position.x = stage.pivot.x = renderWidth / 2;
  stage.position.y = stage.pivot.y = renderHeight / 2;
  var selectedLayer = new PIXI.DisplayGroup(2, false);
  var standardLayer = new PIXI.DisplayGroup(-2, false);
  
  function init()
  {
    console.log('fin');
    for (var i = 0; i < features.length; ++i)
    {
      features[i].spriteId = i;
      portraits.push(new PIXI.Sprite(PIXI.Texture.fromFrame("p" + features[i].id + ".png")));
      portraits[i].featureId = i;
      portraits[i].anchor.x = 0.5;
      portraits[i].anchor.y = 0.5;
      portraits[i].buttonMode = true;
      portraits[i].interactive = true;
      portraits[i].position.x = Math.floor(spacingx * (i % 50 + 0.5)) + spacingx / 2;
      portraits[i].position.y = Math.floor(spacingy * (Math.floor(i / 50) + 0.5)) + spacingy / 2;
      portraits[i].displayGroup = standardLayer;
      portraits[i]
        .on('mouseover', function() {
          this.scale.x = this.scale.y = hover_scale;
          this.displayGroup = selectedLayer;
        })
        .on('mouseout', function() {
          this.scale.x = this.scale.y = base_scale;
          this.displayGroup = standardLayer;
        })
        .on('click', function() {
          if (features[this.featureId].label == null)
            features[this.featureId].label = 'No additional information about this portrait.';
          if (features[this.featureId].title == null)
            features[this.featureId].title = 'Untitled';
          if (features[this.featureId].artist == null)
            features[this.featureId].artist = ['Unknown'];
          if (features[this.featureId].subject == null)
            features[this.featureId].subject = ['Unknown'];
          $('#portrait_title').text(features[this.featureId].title);
          $('#portrait_info').html(
            '<img style="max-width: 300px; max-height: 400px; float: left;" src="' + features[this.featureId].url + '"/>' +
              '<b>Title:</b> ' + features[this.featureId].title + '<br/>' +
              '<b>Artist:</b> ' + features[this.featureId].artist.join(', ') + '<br/>' +
              '<b>Subject:</b> ' + features[this.featureId].subject.join(', ') + '<br/><br/>' +
              features[this.featureId].label + '<br/><h3>Related news</h3><div id="portrait_news">Loading news...</div>'
          );
          $.getJSON("http://search.abc.net.au/s/search.json?query=" + features[this.featureId].subject.join(' ') + "&collection=abcall_meta&form=simple&callback=?", function(json) {
            results = json.response.resultPacket.results;
            $('#portrait_news').html("");
            if (results.length == 0)
              $('#portrait_news').html('No relevant news.');
            for (var t = 0; t < 5 && t < results.length; ++t)
              $('#portrait_news').append(
                '<a href="' + results[t].clickTrackingUrl + '">' + results[t].title + '</a><br/>' +
                '<p>' + results[t].summary + '</p><br/>'
              );
          });
          $('#portrait_content').text(features[this.featureId].title);
          $('#myModal').modal();
        });
      portraits[i].scale.x = portraits[i].scale.y = base_scale;
      portraits[i].target = {
        scale: 1,
        x: portraits[i].position.x,
        y: portraits[i].position.y,
        rotation: portraits[i].rotation
      }

      stage.addChild(portraits[i]);
    }

    circle();
    animate();
  }
  function animate() {
    requestAnimationFrame(animate);
    for (var i = 0; i < portraits.length; ++i)
    {
      if (Math.abs(portraits[i].rotation - portraits[i].target.rotation) > 0.3)
        portraits[i].rotation += 0.2;
      if (portraits[i].position.x > portraits[i].target.x + 10)
        portraits[i].position.x -= 9;
      if (portraits[i].position.x < portraits[i].target.x - 10)
        portraits[i].position.x += 9;
      if (portraits[i].position.y > portraits[i].target.y + 10)
        portraits[i].position.y -= 9;
      if (portraits[i].position.y < portraits[i].target.y - 10)
        portraits[i].position.y += 9;
    }
    pulse += 0.02;
    var sin = Math.sin(pulse);
    var sin2 = Math.sin(pulse * 2);
    stage.scale.y = stage.scale.x = sin * 0.01 + 0.9;
    //stage.rotation = sin2 * 0.02;
    //stage.rotation += 0.001;
    // render the container
    renderer.render(stage);
  }
  function brightnessCircle() {
    sfeatures.sort(function(b, a) {
      return features[a].brightness - features[b].brightness;
    });
    circle();
  }
  function dateCircle() {
    sfeatures.sort(function(a, b) {
      return features[a].date_created - features[b].date_created;
    });
    circle();
  }
  function mediaCircle() {
    sfeatures.sort(function(a, b) {
      if (features[a].media == null)
        return -1;
      if (features[b].media == null)
        return 1;
      return features[a].media.localeCompare(features[b].media);
    });
    circle();
  }
  function circle() {
    var randomness = 100;
    var distance = 5;
    var memberLimit = 4;
    var members = 0;
    for (var i = 0; i < features.length; ++i)
    {
      ++members;
      portraits[features[sfeatures[i]].spriteId].target.x = renderMiddle.x + Math.sin(6.285 * members / memberLimit) * (Math.random() * randomness - randomness / 2 + distance) * 1.3;
      portraits[features[sfeatures[i]].spriteId].target.y = renderMiddle.y + Math.cos(6.285 * members / memberLimit) * (Math.random() * randomness - randomness / 2 + distance);
      if (members > memberLimit)
      {
        members = 0;
        memberLimit += 5;
        distance += 10;
      }
    }
  }
  function setVisibleMedia(media, visible) {
    console.log(media);
    console.log(visible);
    if (media == 'Unknown')
      media = null;
    for (var i = 0; i < features.length; ++i)
    {
      if (features[i].media == media)
        portraits[features[i].spriteId].visible = visible;
    }
  }
</script>
<div id="controls">
  <h1>Portrait</h1><h2>Landscape</h2><br/>
  <button type="button" onclick="brightnessCircle()" class="btn btn-default">Luminosity</button><br/>
  <button type="button" onclick="dateCircle()" class="btn btn-default">Date</button><br/>
  <button type="button" onclick="mediaCircle()" class="btn btn-default">Media</button><br/>
<?php
  $medias = array('Decorative Arts', 'Digital Media', 'Drawings', 'Mixed Media', 'Paintings', 'Photography', 'Prints', 'Sculpture', 'Textiles', 'Unknown');
  foreach ($medias as $media)
  {
?>
  <div class="checkbox">
    <label><input type="checkbox" value="<?php echo $media; ?>" checked><?php echo $media; ?></label>
  </div>
<?php
  }
?>
</div>
<script>
  $(':checkbox').click(function() { 
    setVisibleMedia($(this)[0].value, $(this).is(':checked'))
  });
</script>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="portrait_title"></h4>
      </div>
      <div class="modal-body" id="portrait_info">
        <div id="portrait_news"></div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
</body>
</html>