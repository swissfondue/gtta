/**
 * Cell types
 * @type {string}
 */
mxCell.prototype.TYPE_CHECK = "check";
mxCell.prototype.TYPE_FILTER = "filter";

/**
 * Returns true if cell is "check" type
 * @returns {boolean}
 */
mxCell.prototype.isCheck = function () {
    var type = this.getAttribute("type");
    return type == this.TYPE_CHECK;
};

/**
 * Returns true if cell is "filter" type
 * @returns {boolean}
 */
mxCell.prototype.isFilter = function () {
    var type = this.getAttribute("type");
    return type == this.TYPE_FILTER;
};