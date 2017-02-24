if (typeof oakcms == "undefined" || !oakcms) {
    var oakcms = {};
}

oakcms.tree = {
    init: function() {
        $('.oakcms-tree-toggle').on('click', this.toggle)
        return true;
    },

    toggle: function() {
        $(this).parent("div").parent("div").parent("li").find("ul").toggle("slow");
        return false;
    }
};

oakcms.tree.init();
