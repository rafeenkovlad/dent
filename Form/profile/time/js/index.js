var card = document.querySelector('#sample-card');
var button = document.querySelector('#sample-button');
var paragraph = document.querySelector('#sample-paragraph');
var words = paragraph.innerText.split(' ');
var wordsMarkedUp = words.map(function (word) {
  return '<div class="muddle-word">' + word + '</div>\n  <span> </span>';
});

var viewWidth = window.innerWidth;
var viewHeight = window.innerHeight;
var midX = viewWidth / 2;
var midY = viewHeight / 2;

function setDimensions() {
  viewWidth = window.innerWidth;
  viewHeight = window.innerHeight;
  midX = viewWidth / 2;
  midY = viewHeight / 2;
}

window.addEventListener('resize', setDimensions);

paragraph.innerHTML = wordsMarkedUp.join('');
paragraph.style.opacity = 1;

var wordNodes = paragraph.querySelectorAll('.muddle-word');

function zoomOut(node) {
  var reverse = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

  var visible = { opacity: 1, z: 0 };
  var hidden = { opacity: 0, z: -1000 };
  var start = reverse ? hidden : visible;
  var end = reverse ? visible : hidden;
  node.style.transform = transform(start);
  node.style.opacity = start.opacity;
  return new TWEEN.Tween(start).to(end, 300).onUpdate(function () {
    node.style.transform = transform(this);
    node.style.opacity = Math.min(this.opacity, 1);
  }).easing(TWEEN.Easing.Elastic.Out).start();
}

function zoomIn(node) {
  var reverse = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

  var contracted = { opacity: 0, z: -800, depth: 8, s: 0.7 };
  var expanded = { opacity: 1, z: 0, depth: 10, s: 1 };
  var start = reverse ? expanded : contracted;
  var end = reverse ? contracted : expanded;
  var opacityMax = 0;
  node.style.transform = transform(start);
  node.style.opacity = start.opacity;
  node.style.boxShadow = '0 0 ' + start.depth + 'px 0 #444';
  return new TWEEN.Tween(start).to(end, 600).onUpdate(function () {
    node.style.transform = transform(this);
    node.style.boxShadow = '0 0 ' + this.depth + 'px 0 #444';
    // Only ever *increase* opacity (for elastic/bounce)
    if (this.opacity > opacityMax && !reverse) {
      node.style.opacity = Math.min(this.opacity, 1);
      opacityMax = Math.min(this.opacity, 1);
    }
  }).easing(TWEEN.Easing.Elastic.Out).start();
}

var staggerTweens = void 0;

function staggerZoom(nodeList) {
  var reverse = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

  if (staggerTweens) {
    staggerTweens.forEach(function (t) {
      t.stop();
    });
  }
  var tweens = [];

  var _loop = function _loop() {
    var node = nodeList[i];
    var hidden = { opacity: 0, y: Math.random() * -50, z: -200 };
    var visible = { opacity: 1, y: 0, z: 0 };
    var start = reverse ? visible : hidden;
    var end = reverse ? hidden : visible;
    var opacityMin = 1;
    node.style.transform = transform(0, start.y, start.z);
    node.style.opacity = start.opacity;
    tweens.push(new TWEEN.Tween(start).to(end, 500 + 20 * i).onUpdate(function () {
      node.style.transform = transform(this);
      // Only ever *decrease* opacity
      if (this.opacity < opacityMin && reverse) {
        node.style.opacity = Math.max(this.opacity, 0);
        opacityMin = Math.max(this.opacity, 0);
      } else if (!reverse) {
        node.style.opacity = this.opacity;
      }
    }).easing(TWEEN.Easing.Elastic.Out).delay(20).start());
  };

  for (var i = 0; i < nodeList.length; i++) {
    _loop();
  }
  staggerTweens = tweens;
}

function metricOrZero(val, metric) {
  return '' + val + (val === 0 ? '' : metric);
}

function degreeValue(val) {
  return metricOrZero(val, 'deg');
}

function pixelValue(val) {
  return metricOrZero(val, 'px');
}

function transform(values) {
  return translate3d(values) + ' ' + rotate3(values) + ' ' + scale(values);
}

function scale(values) {
  return 'scale(' + (values.s || 1) + ')';
}

function rotate3(values) {
  var x = degreeValue(values.rx || 0);
  var y = degreeValue(values.ry || 0);
  var z = degreeValue(values.rz || 0);
  return 'rotateX(' + x + ') rotateY(' + y + ') rotateZ(' + z + ')';
}

function translate3d(values) {
  var x = pixelValue(values.x || 0);
  var y = pixelValue(values.y || 0);
  var z = pixelValue(values.z || 0);
  return 'translate3d(' + x + ', ' + y + ', ' + z + ')';
}

var cardExpanded = false;

card.addEventListener('click', function () {
  zoomIn(card, cardExpanded);
  zoomOut(button, cardExpanded);
  staggerZoom(wordNodes, cardExpanded);
  cardExpanded = !cardExpanded;
});

requestAnimationFrame(animate);
function animate(time) {
  requestAnimationFrame(animate);
  TWEEN.update(time);
}