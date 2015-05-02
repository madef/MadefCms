Autoresize = function(textarea)
{
    var self = this;

    this.textarea = textarea;

    this.textarea.onkeydown = function() {
        self.resize();
    }

    this.resize();

    return this;
}

Autoresize.prototype.resize = function()
{
    this.textarea.style.height = this.textarea.scrollHeight;
    console.log(this.textarea);
    do {
        this.textarea.style.height = this.textarea.scrollHeight + 1;
        var lastSize = this.textarea.scrollHeight;
    } while (this.textarea.scrollHeight == lastSize)
    console.log(this.textarea);
    //while($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))) {
        //$(this).height($(this).height()+1);
    //};
}
