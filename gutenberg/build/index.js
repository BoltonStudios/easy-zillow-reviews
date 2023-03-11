/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ (function(module) {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ (function(module) {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/compose":
/*!*********************************!*\
  !*** external ["wp","compose"] ***!
  \*********************************/
/***/ (function(module) {

module.exports = window["wp"]["compose"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ (function(module) {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["i18n"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
!function() {
/*!********************************!*\
  !*** ./gutenberg/src/index.js ***!
  \********************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);










const generalOptions = zillow_data[0].general_options;
const apis = zillow_data[0].available_apis;
(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__.registerBlockType)('boltonstudios/easy-zillow-reviews', {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Zillow Reviews', 'easy-zillow-reviews'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Display reviews from Zillow on your site.', 'easy-zillow-reviews'),
  icon: 'star-filled',
  category: 'widgets',
  attributes: {
    reviewsType: {
      type: 'string',
      default: generalOptions.available_apis
    },
    reviewsLayout: {
      type: 'string',
      default: generalOptions.ezrwp_layout
    },
    gridColumns: {
      type: 'number',
      default: parseInt(generalOptions.ezrwp_cols)
    },
    reviewsCount: {
      type: 'number',
      default: parseInt(generalOptions.ezrwp_count)
    },
    wordLimit: {
      type: 'number',
      default: parseInt(generalOptions.ezrwp_word_limit)
    }
  },
  example: {
    attributes: {
      reviewsType: 'professional',
      reviewsLayout: 'grid',
      gridColumns: 2,
      reviewCount: 2,
      wordLimit: 750
    }
  },
  edit: function (props) {
    const {
      InspectorControls
    } = wp.editor;
    const layout = props.attributes.reviewsLayout;
    const columns = props.attributes.gridColumns;
    const count = props.attributes.reviewsCount;
    const type = props.attributes.reviewsType;
    const wordLimit = props.attributes.wordLimit;
    const ReviewsControl = apis => {
      var apiOptions = []; // dictionary
      apis.forEach(element => {
        apiOptions.push({
          value: element[0],
          label: element[1]
        });
      });
      const control = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
        label: "Select Review Type",
        value: type,
        options: apiOptions,
        onChange: reviewsType => props.setAttributes({
          reviewsType
        })
      });
      return control;
    };
    const LayoutControl = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.SelectControl, {
      label: "Select Layout",
      value: layout,
      options: [{
        value: 'list',
        label: 'List'
      }, {
        value: 'grid',
        label: 'Grid'
      }],
      onChange: reviewsLayout => props.setAttributes({
        reviewsLayout
      })
    });
    const GridControl = reviewsLayout => {
      var control = null;

      // Only display the Grid Columns range control if the 'grid' layout is selected
      if (reviewsLayout == 'grid') {
        control = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.RangeControl, {
          beforeIcon: "arrow-left-alt2",
          afterIcon: "arrow-right-alt2",
          label: "Grid Columns",
          value: columns,
          onChange: gridColumns => props.setAttributes({
            gridColumns
          }),
          min: 1,
          max: 6
        });
      }
      return control;
    };
    const ReviewsCountControl = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.RangeControl, {
      beforeIcon: "arrow-left-alt2",
      afterIcon: "arrow-right-alt2",
      label: "Reviews Count",
      value: count,
      onChange: reviewsCount => props.setAttributes({
        reviewsCount
      }),
      min: 1,
      max: 10
    });
    const WordLimitControl = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.RangeControl, {
      beforeIcon: "arrow-left-alt2",
      afterIcon: "arrow-right-alt2",
      label: "Excerpt Length",
      value: wordLimit,
      onChange: wordLimit => props.setAttributes({
        wordLimit
      }),
      min: 20,
      max: 750
    });

    // Append the grid class name to the reviews wrapper if the user selected the grid layout.
    function getWrapperLayoutClass(reviewsLayout, gridColumns) {
      var className = '';
      if (reviewsLayout == 'grid') {
        className = 'ezrwp-grid ezrwp-grid-' + gridColumns;
      }
      return className;
    }

    // Assemble the review placeholders.
    function getReviewPlaceholders(reviewsLayout, gridColumns, reviewsCount) {
      var reviews = [];
      var layout = reviewsLayout;
      var columns = gridColumns;
      var count = reviewsCount;
      for (var i = 1; i <= count; i++) {
        reviews.push((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
          className: "col ezrwp-col"
        }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("ul", {
          className: "ezrwp-placeholder-text blockquote-placeholder"
        }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", null), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", null), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", null), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", null), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", null)), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
          className: "ezrwp-stars ezrwp-stars-5"
        }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
          className: "ezrwp-date"
        }, "Zillow Review"), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
          className: "ezrwp-reviewer-placeholder"
        }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("ul", {
          className: "ezrwp-placeholder-text"
        }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
          class: "attribution"
        }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
          class: "link"
        }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
          class: "text"
        })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", null), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", null)))));

        // Add spacer between rows of columns
        if (i % columns == 0) {
          reviews.push((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
            class: "clear"
          }));
        }

        // Add spacer between rows in List layout
        if (layout == 'list') {
          reviews.push((0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
            class: "clear"
          }));
        }
      }
      return reviews;
    }
    return [(0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(InspectorControls, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__.PanelBody, null, ReviewsControl(apis), LayoutControl, GridControl(layout), ReviewsCountControl, WordLimitControl)), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: props.className
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "ezrwp-wrapper " + getWrapperLayoutClass(layout, columns)
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "ezrwp-content"
    }, getReviewPlaceholders(layout, columns, count, wordLimit))))];
  }
});
}();
/******/ })()
;
//# sourceMappingURL=index.js.map