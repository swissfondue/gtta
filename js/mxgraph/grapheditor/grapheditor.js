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

mxCell.prototype.setActive = function () {

};

/**
 * Erase cell's style
 */
mxCell.prototype.delStyle = function () {
    this.setStyle('');
};