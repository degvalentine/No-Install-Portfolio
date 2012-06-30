/**
* Plugin for playing a closed audiodescription with a video.
**/
package com.jeroenwijering.plugins {


import com.jeroenwijering.events.*;
import com.jeroenwijering.utils.Logger;

import flash.display.*;
import flash.events.*;
import flash.media.*;
import flash.net.*;
import flash.utils.ByteArray;


public class Audiodescription extends MovieClip implements PluginInterface {


	[Embed(source="../../../controlbar.png")]
	private const ControlbarIcon:Class;
	[Embed(source="../../../dock.png")]
	private const DockIcon:Class;


	/** Reference to the dock button. **/
	private var button:MovieClip;
	/** Sound channel object. **/
	private var channel:SoundChannel;
	/** Current volume. **/
	private var current:Number = -1;
	/** List with configuration settings. **/
	public var config:Object = {
		ducking:0,
		debug:false,
		file:undefined,
		state:true,
		volume:90
	};
	/** Array with ducking samples, used for the ducking. **/
	private var ducks:Array;
	/** Reference to the icon. **/
	private var icon:Bitmap;
	/** Array with waveform samples, used for the ducking. **/
	private var waves:Array;
	/** A bunch of clips drawn for the debug menu. **/
	private var clips:Object;
	/** sound object to be instantiated. **/
	private var sound:Sound;
	/** Reference to the MVC view. **/
	private var view:AbstractView;
	/** Volume of the original audio. **/
	private var original:Number;
	/** Is the sound already loaded. **/
	private var loaded:Boolean;


	/** Constructor; not much going on. **/
	public function Audiodescription():void {};


	/** Initing the plugin. **/
	public function initializePlugin(vie:AbstractView):void {
		view = vie;
		view.addControllerListener(ControllerEvent.ITEM,itemHandler);
		view.addModelListener(ModelEvent.TIME,timeHandler);
		view.addModelListener(ModelEvent.STATE,stateHandler);
		if(view.config['dock']) {
			button = view.getPlugin('dock').addButton(DisplayObject(new DockIcon()),'is on',clickHandler);
		} else if(view.getPlugin('controlbar')) {
			icon = new ControlbarIcon();
			view.getPlugin('controlbar').addButton(icon,'audiodescription',clickHandler);
		}
		original = view.config['volume'];
		setState(config['state']);
		if(config['debug']) {
			drawClips();
		}
	};


	/** Clicking the  hide button. **/
	private function clickHandler(evt:MouseEvent):void {
		setState(!config['state']);
	};


	/** Extract a waveform of the sound when it's completed. **/
	private function completeHandler(evt:Event):void {
		var arr:ByteArray;
		var bts:Number = 4410;
		var pos:Number;
		// load a sound sample every .1 second.
		while(bts == 4410) {
			arr = new ByteArray();
			bts = sound.extract(arr,4410);
			arr.position = 0;
			if(arr.bytesAvailable > 7) {
				waves.push(arr.readFloat());
			}
		}
		if(config['debug']) { drawWave() };
		// quantify the ducking and add some margin.
		var i:Number = 0;
		while(i<waves.length) {
			if(waves[i] == 0) {
				ducks.push(0);
				i++;
			} else {
				ducks[i-2] = ducks[i-1] = 1;
				ducks.push(1,1,1);
				i += 3;
			}
		}
		// Smooth out the ducking.
		var blr:Number = 5;
		var tmp:Array = new Array();
		for(i=0; i <= blr; i++) { tmp.push(ducks[i]); }
		for(i=blr; i < ducks.length-blr; i++) {
			var ent:Number = 0;
			for(var j:Number = i - blr; j <= i + blr; j++) { 
				ent += ducks[j];
			}
			tmp.push( ent / (blr*2+1) );
		}
		for(i=ducks.length-blr; i < ducks.length; i++) { tmp.push(ducks[i]); }
		ducks = tmp;
		if(config['debug']) { drawDucks() };
	};


	/** Draw the overall clips. **/
	private function drawClips():void {
		var back:MovieClip = new MovieClip();
		back.graphics.beginFill(0x000000,0.8);
		back.graphics.drawRect(0,0,view.config['width'],100);
		addChild(back);
		var wave:MovieClip = new MovieClip();
		wave.y = 50;
		addChild(wave);
		var duck:MovieClip = new MovieClip();
		addChild(duck);
		var line:MovieClip = new MovieClip();
		line.graphics.beginFill(0xFF0000);
		line.graphics.drawRect(0,0,1,100);
		addChild(line);
		clips = {back:back,wave:wave,duck:duck,line:line};
	};


	/** Draw the audiodescription waveform. **/
	private function drawDucks():void {
		clips.duck.graphics.moveTo(0,0);
		clips.duck.graphics.lineStyle(1,0x0000FF);
		for(var i:Number=0; i<ducks.length; i++) {
			clips.duck.graphics.lineTo(i,100 - original + ducks[i]*config['ducking']);
		}
		clips.duck.width = view.config['width'];
	};


	/** Draw the audiodescription waveform. **/
	private function drawWave():void {
		clips.wave.graphics.moveTo(0,0);
		clips.wave.graphics.lineStyle(1,0x00FF00);
		for(var i:Number=0; i<waves.length; i++) {
			clips.wave.graphics.lineTo(i,waves[i]*50);
		}
		clips.wave.width = view.config['width'];
	};


	/** Check for captions with a new item. **/
	private function itemHandler(evt:ControllerEvent=null):void {
		loaded = false;
		var fil:String = '';
		if(view.config['item'] == 0) {
			fil = view.config['audiodescription.file'];
		}
		if (view.playlist[view.config['item']]['audiodescription.file']) {
			fil = view.playlist[view.config['item']]['audiodescription.file'];
		}
		if(fil) {
			config['file'] = fil;
		} else { 
			config['file'] = undefined;
		}
		ducks = new Array();
		waves = new Array();
		if(config['debug']) {
			clips.duck.graphics.clear();
			clips.wave.graphics.clear();
		}
	};


	/** Turn the audiodescription on/off. **/
	public function setState(stt:Boolean):void {
		config['state'] = stt;
		var cke:SharedObject = SharedObject.getLocal('com.jeroenwijering','/');
		cke.data['audiodescription.state'] = stt;
		cke.flush();
		setVolume(config['volume']);
		if(stt) {
			if(button) {
				button.field.text = 'is on';
			} else {
				icon.alpha = 1;
			}
		} else {
			if(button) {
				button.field.text = 'is off';
			} else {
				icon.alpha = 0.3;
			}
		}
	};


	/** Set the volume level. **/
	private function setVolume(vol:Number):void {
		var trf:SoundTransform = new SoundTransform(vol/100);
		if(!config['state']) { trf.volume = 0; }
		if(channel) { channel.soundTransform = trf; }
	};


	/** The statehandler manages audio pauses. **/
	private function stateHandler(evt:ModelEvent):void {
		switch(evt.data.newstate) {
			case ModelStates.PAUSED:
			case ModelStates.COMPLETED:
			case ModelStates.IDLE:
				if(loaded) { channel.stop(); }
				break;
			case ModelStates.PLAYING:
				if(config['file'] && !loaded) {
					try {
						sound = new Sound(new URLRequest(config['file']));
						if(config['ducking']) {
							sound.addEventListener(Event.COMPLETE,completeHandler);
						}
						channel = sound.play();
						setVolume(config['volume']);
						loaded = true;
					} catch (err:Error) {
						Logger.log(err.message,'audiodescription');
					}
				}
				break;
		}
	};


	/** Check timing of the player to sync audio if needed. **/
	private function timeHandler(evt:ModelEvent):void {
		var pos:Number = evt.data.position;
		if(loaded && view.config['state'] == ModelStates.PLAYING && Math.abs(pos-channel.position/1000) > 0.5) {
			channel.stop();
			channel = sound.play(pos*1000);
			setVolume(config['volume']);
		}
		if(config['ducking'] && config['state'] && ducks.length > 0 && ducks[Math.round(pos*10)] != current) {
			current = ducks[Math.round(pos*10)];
			view.sendEvent(ViewEvent.VOLUME,original-ducks[Math.round(pos*10)]*config['ducking']);
		}
		if(config['debug']) { 
			clips.line.x = Math.round(evt.data.position/evt.data.duration*view.config['width']);
		}
	};


};


}