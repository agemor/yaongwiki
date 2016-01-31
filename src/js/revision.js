/**
 * YaongWiki Engine
 *
 * @version 1.1
 * @author HyunJun Kim
 * @date 2016. 01. 31
 */

var contentDiff = JsDiff.diffChars(original.snapshot_content, revision.snapshot_content);
var display = document.getElementById('content');
contentDiff.forEach(function(part){
  var container; 
  if (part.added) {
    container = document.createElement('mark');
    container.style.color = 'green';

  } else if (part.removed) {
  	container = document.createElement('s');
  	container.style.color = 'red';
  }  else {
  	container = document.createElement('span');
  }
  container.appendChild(document.createTextNode(part.value));
  display.appendChild(container);
});

var tagsDiff = JsDiff.diffChars(original.snapshot_tags, revision.snapshot_tags);
var tagsContainer = document.getElementById('tags');
tagsDiff.forEach(function(part){
  var container; 
  if (part.added) {
    container = document.createElement('mark');
    container.style.color = 'green';

  } else if (part.removed) {
  	container = document.createElement('s');
  	container.style.color = 'red';
  }  else {
  	container = document.createElement('span');
  }
  container.appendChild(document.createTextNode(part.value));
  tagsContainer.appendChild(container);
});