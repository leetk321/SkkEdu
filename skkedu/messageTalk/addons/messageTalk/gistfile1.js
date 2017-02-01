AudioSFX = function( _p, _autoplay ) {
    var self = this;
    var parent = _p;
 
    this.sounds = new Array();
 
    this.context = new webkitAudioContext();
    this.autoplay = _autoplay;
 
    this.play = function( sound ) {
        var source = self.context.createBufferSource();
        source.buffer = self.sounds[sound];
        source.connect( self.context.destination );
        source.noteOn( 0 );
    }
 
    this.load = function() {
        var request = new XMLHttpRequest();
        request.addEventListener( 'load', function(e) {
            self.context.decodeAudioData( request.response, function(decoded_data) {
                self.sounds[0] = decoded_data;
                if( self.autoplay ) {
                    self.play(0);
                }
            }, function(e){
                console.log("error");
            });
        }, false);
        request.open( 'GET', 'wav/sfx.wav', true );
        request.responseType = "arraybuffer";
        request.send();
    }
 
    self.load();
}
 
var sfx = new AudioSFX( this, true ); // true for autoplay