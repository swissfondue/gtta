/**
 * Returns true if cell is "check" type
 * @returns {boolean}
 */
mxCell.prototype.isCheck = function () { 
    var type = this.getAttribute("type");
    return type == admin.mxgraph.CELL_TYPE_CHECK;
};

/**
 * Returns true if cell is a start check
 * @returns {boolean|Number}
 */
mxCell.prototype.isStartCheck = function () {
    var starter = parseInt(this.getAttribute("start_check"));

    return this.isCheck() && starter;
};

/**
 * Returns true if cell is "filter" type
 * @returns {boolean}
 */
mxCell.prototype.isFilter = function () {
    var type = this.getAttribute("type");
    return type == admin.mxgraph.CELL_TYPE_FILTER;
};

/**
 * Erase cell's style
 */
mxCell.prototype.delStyle = function () {
    this.setStyle('');
};

var stoppedCellStyle = {};
stoppedCellStyle[mxConstants.STYLE_STROKECOLOR] = '#FF0000';
stoppedCellStyle[mxConstants.STYLE_STROKEWIDTH] = 2;

var noCheckSelectedStyle = {};
noCheckSelectedStyle[mxConstants.STYLE_STROKECOLOR] = '#FFCC00';
noCheckSelectedStyle[mxConstants.STYLE_STROKEWIDTH] = 2;