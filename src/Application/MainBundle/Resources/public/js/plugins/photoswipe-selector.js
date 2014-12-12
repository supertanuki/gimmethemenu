var initPhotoSwipeFromDOM = function(gallerySelector) {

    // parse slide data (url, title, size ...) from DOM elements (links)
    var parseThumbnailElements = function(el) {
        var thumbElements = el.children,
            numNodes = thumbElements.length,
            items = [],
            el,
            childElements,
            thumbnailEl,
            size,
            item;

//            alert(numNodes);

        for(var i = 0; i < numNodes; i++) {
            var el = thumbElements[i];
            var el = el.children;
            var el = el[0];
            var el = el.children;
            var el = el[0];

            // include only element nodes
            if(el.nodeType !== 1) {
                continue;
            }

            childElements = el.children;

            size = el.getAttribute('data-size').split('x');

            // create slide object
            item = {
                src: el.getAttribute('href'),
                title: el.getAttribute('title'),
                msrc: el.getAttribute('data-msrc'),
                w: parseInt(size[0], 10),
                h: parseInt(size[1], 10)
            };

            item.el = el; // save link to element for getThumbBoundsFn

            items.push(item);
        }

        return items;
    };

    // find nearest parent element
    var closest = function closest(el, fn) {
        return el && ( fn(el) ? el : closest(el.parentNode, fn) );
    };

    // triggers when user clicks on thumbnail
    var onThumbnailsClick = function(e) {
        e = e || window.event;
        e.preventDefault ? e.preventDefault() : e.returnValue = false;

        var eTarget = e.target || e.srcElement;

        var clickedListItem = closest(eTarget, function(el) {
            return el.tagName === 'A';
        });

        if (!clickedListItem) {
            return;
        }

        var clickedGallery = clickedListItem.parentNode.parentNode.parentNode;

        var childNodes = clickedGallery.children,
            numChildNodes = childNodes.length,
            nodeIndex = 0,
            index;

        for (var i = 0; i < numChildNodes; i++) {
            var child = childNodes[i].children;
            var child = child[0].children;
            var child = child[0];

            if(child === clickedListItem) {
                index = nodeIndex;
                break;
            }
            nodeIndex++;
        }

        if(index >= 0) {
            openPhotoSwipe( index, clickedGallery );
        }
        return false;
    };

    // parse picture index and gallery index from URL (#&pid=1&gid=2)
    var photoswipeParseHash = function() {
        var hash = window.location.hash.substring(1),
            params = {};

        if(hash.length < 5) {
            return params;
        }

        var vars = hash.split('&');
        for (var i = 0; i < vars.length; i++) {
            if(!vars[i]) {
                continue;
            }
            var pair = vars[i].split('=');
            if(pair.length < 2) {
                continue;
            }
            params[pair[0]] = pair[1];
        }

        if(params.gid) {
            params.gid = parseInt(params.gid, 10);
        }

        if(!params.hasOwnProperty('pid')) {
            return params;
        }
        params.pid = parseInt(params.pid, 10);
        return params;
    };

    var openPhotoSwipe = function(index, galleryElement, disableAnimation) {
        var pswpElement = document.querySelectorAll('.pswp')[0],
            gallery,
            options,
            items;

        items = parseThumbnailElements(galleryElement);

        // define options (if needed)
        options = {
            index: index,
            fullscreenEl: false,
            zoomEl: true,
            shareEl: false,

            // define gallery index (for URL)
            galleryUID: galleryElement.getAttribute('data-pswp-uid'),

            getThumbBoundsFn: function(index) {
                // See Options -> getThumbBoundsFn section of docs for more info
                var thumbnail = items[index].el.children[0],
                    pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
                    rect = thumbnail.getBoundingClientRect();

                return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
            }

        };

        if(disableAnimation) {
            options.showAnimationDuration = 0;
        }

        // Pass data to PhotoSwipe and initialize it
        gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
        gallery.init();
    };

    // loop through all gallery elements and bind events
    var galleryElements = document.querySelectorAll( gallerySelector );
    for(var i = 0, l = galleryElements.length; i < l; i++) {
        galleryElements[i].setAttribute('data-pswp-uid', i+1);
        galleryElements[i].onclick = onThumbnailsClick;
    }

    // Parse URL and open gallery if it contains #&pid=3&gid=1
    var hashData = photoswipeParseHash();
    if(hashData.pid > 0 && hashData.gid > 0) {
        openPhotoSwipe( hashData.pid - 1 ,  galleryElements[ hashData.gid - 1 ], true );
    }
};