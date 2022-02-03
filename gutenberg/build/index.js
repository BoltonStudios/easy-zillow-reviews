/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
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










var generalOptions = zillow_data[0].general_options;
var apis = zillow_data[0].available_apis;
console.log(zillow_data[0]);
Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__["registerBlockType"])('boltonstudios/easy-zillow-reviews', {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Zillow Reviews', 'easy-zillow-reviews'),
  description: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Display reviews from Zillow on your site.', 'easy-zillow-reviews'),
  icon: 'star-filled',
  category: 'widgets',
  attributes: {
    screenname: {
      type: 'string',
      default: "test"
    },
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
      default: generalOptions.ezrwp_cols
    },
    reviewsCount: {
      type: 'number',
      default: generalOptions.ezrwp_count
    }
  },
  example: {
    attributes: {
      reviewsType: 'professional',
      reviewsLayout: 'grid',
      gridColumns: '2',
      reviewCount: '2'
    }
  },
  edit: function edit(props) {
    var InspectorControls = wp.editor.InspectorControls;
    var screenname = props.attributes.screenname;
    var layout = props.attributes.reviewsLayout;
    var columns = props.attributes.gridColumns;
    var count = props.attributes.reviewsCount;
    var reviewsType = props.attributes.reviewsType; //

    var ScreennameControl = function ScreennameControl(reviewsType) {
      // Initialize variables.
      var control = null; // Only display the Screenname TextControl if the 'professional' type is selected.

      if (reviewsType == 'professional') {
        control = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["TextControl"], {
          label: "Screenname",
          value: screenname,
          onChange: function onChange(screenname) {
            return props.setAttributes({
              screenname: screenname
            });
          }
        });
      }

      return control;
    }; //


    var ReviewsControl = function ReviewsControl(apis) {
      var apiOptions = []; // dictionary

      apis.forEach(function (element) {
        apiOptions.push({
          value: element[0],
          label: element[1]
        });
      });
      var control = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["SelectControl"], {
        label: "Select Review Type",
        value: reviewsType,
        options: apiOptions,
        onChange: function onChange(reviewsType) {
          return props.setAttributes({
            reviewsType: reviewsType
          });
        }
      });
      return control;
    }; //


    var LayoutControl = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["SelectControl"], {
      label: "Select Layout",
      value: layout,
      options: [{
        value: 'list',
        label: 'List'
      }, {
        value: 'grid',
        label: 'Grid'
      }],
      onChange: function onChange(reviewsLayout) {
        return props.setAttributes({
          reviewsLayout: reviewsLayout
        });
      }
    }); //

    var GridControl = function GridControl(reviewsLayout) {
      var control = null; // Only display the Grid Columns range control if the 'grid' layout is selected

      if (reviewsLayout == 'grid') {
        control = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["RangeControl"], {
          beforeIcon: "arrow-left-alt2",
          afterIcon: "arrow-right-alt2",
          label: "Grid Columns",
          value: columns,
          onChange: function onChange(gridColumns) {
            return props.setAttributes({
              gridColumns: gridColumns
            });
          },
          min: 1,
          max: 6
        });
      }

      return control;
    }; //


    var ReviewsCountControl = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["RangeControl"], {
      beforeIcon: "arrow-left-alt2",
      afterIcon: "arrow-right-alt2",
      label: "Reviews Count",
      value: count,
      onChange: function onChange(reviewsCount) {
        return props.setAttributes({
          reviewsCount: reviewsCount
        });
      },
      min: 1,
      max: 10
    }); // Append the grid class name to the reviews wrapper if the user selected the grid layout.

    function getWrapperLayoutClass(reviewsLayout, gridColumns) {
      var className = '';

      if (reviewsLayout == 'grid') {
        className = 'ezrwp-grid ezrwp-grid-' + gridColumns;
      }

      return className;
    } // Assemble the review placeholders.


    function getReviewPlaceholders(reviewsLayout, gridColumns, reviewsCount) {
      var reviews = [];
      var layout = reviewsLayout;
      var columns = gridColumns;
      var count = reviewsCount;

      for (var i = 1; i <= count; i++) {
        reviews.push(Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
          className: "col ezrwp-col"
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("ul", {
          className: "ezrwp-placeholder-text blockquote-placeholder"
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("li", null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("li", null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("li", null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("li", null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("li", null)), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
          className: "ezrwp-stars ezrwp-stars-5"
        }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
          className: "ezrwp-date"
        }, "Zillow Review"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
          className: "ezrwp-reviewer-placeholder"
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("ul", {
          className: "ezrwp-placeholder-text"
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("li", {
          class: "attribution"
        }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
          class: "link"
        }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("span", {
          class: "text"
        })), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("li", null), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("li", null))))); // Add spacer between rows of columns

        if (i % columns == 0) {
          reviews.push(Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
            class: "clear"
          }));
        } // Add spacer between rows in List layout


        if (layout == 'list') {
          reviews.push(Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
            class: "clear"
          }));
        }
      }

      return reviews;
    }

    return [Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(InspectorControls, null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["PanelBody"], null, ScreennameControl(reviewsType), ReviewsControl(apis), LayoutControl, GridControl(layout), ReviewsCountControl)), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: props.className
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "ezrwp-wrapper " + getWrapperLayoutClass(layout, columns)
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
      className: "ezrwp-content"
    }, getReviewPlaceholders(layout, columns, count))))];
  }
});

/***/ }),

/***/ "@wordpress/blocks":
/*!*****************************************!*\
  !*** external {"this":["wp","blocks"]} ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

/***/ }),

/***/ "@wordpress/components":
/*!*********************************************!*\
  !*** external {"this":["wp","components"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ "@wordpress/compose":
/*!******************************************!*\
  !*** external {"this":["wp","compose"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "@wordpress/i18n":
/*!***************************************!*\
  !*** external {"this":["wp","i18n"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ })

/******/ });
//# sourceMappingURL=index.js.map