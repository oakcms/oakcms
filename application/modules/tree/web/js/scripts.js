if (typeof pistol88 == "undefined" || !pistol88) {
    var pistol88 = {};
}

pistol88.tree = {
    init: function() {
        $('.pistol88-tree-toggle').on('click', this.toggle)
        return true;
    },
    
    toggle: function() {
        $(this).parent("div").parent("div").parent("li").find("ul").toggle("slow");
        return false;
    }
};

pistol88.tree.init();