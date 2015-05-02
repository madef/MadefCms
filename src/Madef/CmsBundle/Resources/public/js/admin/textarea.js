Autoresize = function(textarea)
{
    var self = this;

    this.textarea = textarea;

    this.textarea.onkeyup = function() {
        self.resize();
    }

    this.resize();

    return this;
}

Autoresize.prototype.resize = function()
{
    var size = this.textarea.scrollHeight;
    do {
        var lastScrollHeight = this.textarea.scrollHeight;
        size++;
        this.textarea.style.height = size + 'px';
    } while (this.textarea.scrollHeight == lastScrollHeight)
}
