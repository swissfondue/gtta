/**
 * Returns true if cell is "check" type
 * @returns {boolean}
 */
mxCell.prototype.isCheck = function () {
    var type = this.getAttribute("type");
    return type == user.mxgraph.CELL_TYPE_CHECK;
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
    return type == user.mxgraph.CELL_TYPE_FILTER;
};

/**
 * Set cell as active check
 */
mxCell.prototype.setActive = function () {
    this.setStyle("STYLE_ACTIVE_CHECK");
};

/**
 * Set cell as start check
 */
mxCell.prototype.setStart = function () {
    this.setStyle("STYLE_START_CHECK");
};

/**
 * Set cell as start and active check
 */
mxCell.prototype.setActiveStart = function () {
    this.setStyle("STYLE_ACTIVE_START_CHECK");
};

/**
 * Set cell as stopped check
 */
mxCell.prototype.setStopped = function () {
    this.setStyle("STYLE_CELL_STOPPED");
};

/**
 * Set cell as no check
 */
mxCell.prototype.setNoCheck = function () {
    this.setStyle("STYLE_NO_CHECK_SELECTED");
};

/**
 * Erase cell's style
 */
mxCell.prototype.delStyle = function () {
    this.setStyle("");
};