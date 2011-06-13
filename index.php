<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="es">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Database Schema</title>
        <script src="raphael-min.js" type="text/javascript" charset="utf-8"></script>
        <script src="graffle.js" type="text/javascript" charset="utf-8"></script>
        <script src="jquery.min.js" type="text/javascript" charset="utf-8"></script>  
        
<?php

  include ('./spyc/spyc.php');
  include ('class_arraytojs.php');
/*
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
*/
  $array = Spyc::YAMLLoad('schema.yml');
  
  $easyconvert = new array_to_js(); 
  $easyconvert->add_array($array, "datos"); 
  echo $easyconvert->output_all(); 

?>
<script type="text/javascript">
// Each of the following examples create a canvas that is 320px wide by 200px high
// Canvas is created at the viewport’s 10,50 coordinate
//function cajaTexto
// Image dump
/*
var paper;
var caja1;
var caja2;
var caja3;
var ta;




var a;
*/

var tablas=new Object();
var conectar = new Function();
var relaciones= new Array();

conectar = function(r){
    /*
    for ( x in r ){
      paper.conection(r[x])
    }
    */
    $.each( r, function (index,value){
      //paper.connection(value.dos, value.uno, value.color, value.color+"|3");
      paper.connection(value);
      paper.safari();
//      value.line.remove();
    });

    paper.safari();
};  
  

window.onload = function(){
  Raphael.fn.cajaTexto = function( x, y , w, h , txt){
    this.x = x;
    this.y = y;
    this.width = w;
    this.height = h;
    this.txt = txt;
    var ct= this;

    var caja = this.rect(this.x, this.y, this.width, this.height);
    var texto = this.text( this.x + caja.getBBox().width/2, 
                        this.y + caja.getBBox().height/2, 
                        this.txt );
                        

    var cajat = this.set();
    cajat.push(caja);
    cajat.push(texto);
    

    
    var oTextbox = {
      nombre: txt,
      dibujo: cajat,
      setX: function(nuevax){
        caja.attr({x: nuevax});
        texto.attr({x: nuevax + caja.getBBox().width/2 })
      },
      setxy: function(nx, ny){
        caja.attr({x: nx, y: ny });
        texto.attr({x: nx + caja.getBBox().width/2, y : ny + caja.getBBox().height/2 })
      },
      setTexto: function(str){
        texto.attr({text: str});
      }

    }
    
    return oTextbox;
  }

  Raphael.fn.arrow = function (x1, y1, x2, y2, size) {
    var angle = Math.atan2(x1-x2,y2-y1);
    angle = (angle / (2 * Math.PI)) * 360;
    var arrowPath = this.path("M" + x2 + " " + y2 + " L" + (x2 - size) + " " + (y2 - size) + " L" + (x2 - size) + " " + (y2 + size) + " L" + x2 + " " + y2 ).attr("fill","black").rotate((90+angle),x2,y2);
    var linePath = this.path("M" + x1 + " " + y1 + " L" + x2 + " " + y2);
    return [linePath,arrowPath];
  }

  Raphael.fn.tabla = function( x, y , values, title ){
    var ancho = 135;
    var alto = 20;
    var key;
    var keys = new Object();
    var fkeys = new Object();
    var self= this;

    
    var titulo = this.cajaTexto(x,y,ancho,alto ,title);
    titulo.dibujo[0].attr({fill: 'white'});
    
    var t = new Array();
    t.push(titulo);

    var campos = values['columns'];
    var cmp= new Array();
    
    //id automatico (no sale el el yaml
    
    var cajaid = this.cajaTexto(x,alto+y, ancho, alto-5, 'id' );
    cajaid.dibujo[0].attr({fill: '#ddd'});
    t.push( cajaid ) ;
    
    key = this.circle(x, y+alto+((alto-5)/2) , 3);
    key.attr({fill: 'yellow'});
    keys['id']= key;

    var i = 1;
    $.each( values['columns'] , function(index, value) {
      //var ij = self.cajaTexto(x,alto+y+(i*(alto-5)), ancho, alto-5, index+': '+value['type'] );
      var ij = self.cajaTexto(x,alto+y+(i*(alto-5)), ancho, alto-5, index );
      ij.dibujo[0].attr({fill: '#ddd'});
      fkey=/_id$/;      
      if ( fkey.test(index) ){
        key = self.circle(x+ancho , alto+y+(i*(alto-5))+((alto-5)/2) , 3);
        key.attr({fill: 'blue'});
        //keys.push(key);
        keys[index]= key;
        fkeys[index]=ij.dibujo;
      }
      t.push(ij);
      i++;
    });
    var agarradera = t[0].dibujo;    
    

    var start = function () {
        // storing original coordinates
        $.each(t , function (index, value){
          $.each(value.dibujo , function (index, value){ 
            value.ox = value.attr("x");
            value.oy = value.attr("y");
            value.oopa= value.attr().opacity ? value.attr().opacity : 1;
            value.attr({opacity: .5});
          });
        });
        $.each( keys , function(index, value) {
            value.ox = value.attr("cx");
            value.oy = value.attr("cy");
            value.oopa= value.attr().opacity ? value.attr().opacity : 1;
            value.attr({opacity: .5});
        });
    },
    move = function (dx, dy) {
      // move will be called with dx and dy
      $.each(t , function (index, value){
        $.each(value.dibujo , function (index, value){ 
          value.attr({x: value.ox + dx, y: value.oy + dy});
          value.toFront();
        });
      });
      $.each( keys , function(index, value) {
        value.attr({cx: value.ox + dx, cy: value.oy + dy});
        value.toFront();
      });
      conectar(relaciones);
    },
    up = function () {
        // restoring state
        $.each(t , function (index, value){
          $.each(value.dibujo , function (index, value){ 
            value.attr({opacity: value.oopa });
            
          });
        });
        $.each(keys , function (index, value){
            value.attr({opacity: value.oopa});
        });
    };
    agarradera.drag(move, start, up);
    agarradera.attr({cursor: "move"});

   
    var o = {
      nombre: title,
      dibujo: t,
      keys: keys,
      key: t[1].dibujo,
      fkeys: fkeys,
      setxy: function(nx, ny){
        var i = 0;
        $.each( t , function(index, value) {
            value.setxy( nx, alto+ny+((i-1)*(alto-5)) );
            i++;
        });
        t[0].setxy( nx, ny );

        $.each( keys , function(index, value) {
            value.attr({ cx: value.attr().cx +( nx-x ) , cy: value.attr().cy +( ny-y ) });
        });
        x= nx; y = ny;
      },
      getX : function() { return x }, 
      getY : function() { return y },
      toFront: function(){
        $.each(t , function (index, value){
          $.each(value.dibujo , function (index, value){ 
            value.toFront();
          });
        });
        $.each( keys , function(index, value) {
          value.toFront();
        });
      }, 
      setOpacity: function(o){
        $.each(t , function (index, value){
          $.each(value.dibujo , function (index, value){ 
            value.attr({opacity: o });
            
          });
        });
        $.each(keys , function (index, value){
            value.attr({opacity: o });
        });
      },
      getHeight: function(){
        return (alto-5) * (t.length-1) + alto;
      }
    }
    
    return o;
  }


  paper = Raphael("holder", 1000,1300);
  /*
  caja1 = paper.cajaTexto(100,10,40,10,"holaaa");
  caja2 = paper.cajaTexto(10,10,50,20,"hola");
  caja3 = paper.cajaTexto(100,200,50,20,"hola22");
  */  
  /*
  var cajas= new Array();
  for (var i=0; i<12; i++){
    var yc = 90;
    var xc=80*i+20;
    if (i > 5){  yc=120; xc=80*(i-6)+20; }
    cajas[i] = paper.cajaTexto(xc,yc,50,20,"Caja "+i);
    paper.connection(caja2.dibujo, cajas[i].dibujo,  "#aaf");
  } 

  paper.connection(caja2.dibujo, caja1.dibujo, "#fff");
  paper.connection(caja2.dibujo, caja3.dibujo, "#000");
  */  
  /*
  var valures= new Array()
  valures['titulo']="titulazo";
  valures['campos']=new Array();
  valures['campos'].push({ titulo: 'uno', clave: 'id'});
  valures['campos'].push({ titulo: 'uno', clave: 'foreign' });
  valures['campos'].push({ titulo: 'tres' });
  ta = paper.tabla(10,300, datos['User'], 'User' );
  */


  var j= 0; 
  
  var anterior = '';
  
  $.each( datos , 
    function(index, value) {
    
        filas=4;
        separacion= 50;

        //tablas[index] =paper.tabla( 10+( (j%6)*160), 10+(500*(Math.floor( j/6 ))), value, index );
        if (j%filas==0){
          yi= 0;
        } else {
          yi = (anterior=='')? 0 : tablas[anterior].getHeight()+tablas[anterior].getY();
        }

        tablas[index] =paper.tabla( 
                          separacion+ ( Math.floor( j/filas )  * (135 + separacion) ), 
                          separacion + yi ,
                          value, index );
        anterior = index;

      j++;
    }
  );
  


  $.each( datos , 
    function(index, value) {
      var padre = index;
      $.each( value , function(index,value){
        if (index=="relations")
          $.each( value , function(index,value){
            if ( !tablas.hasOwnProperty(index) ){
              //alert (index);
              index= value['class'];
              //alert ('index cambiado a '+index);
            }
            
            var uno,dos;
            if (value['foreign']=='id'){
              if (tablas[index] != undefined)
                uno = tablas[index].keys.id;
              else
                alert ("Table '"+index+"' Dont exists in database");
            } else {
              alert(value['foreign']+" uno no id "+ index);
              uno = tablas[index].keys[value['foreign']];
            }
            if (tablas[padre].fkeys[value['local']]!=undefined)
              dos = tablas[padre].fkeys[value['local']];
            
            c1= Math.floor((Math.random()*250)).toString();
            c2= Math.floor((Math.random()*250)).toString();
            c3= Math.floor((Math.random()*250)).toString();

            c4= Math.floor((Math.random()*100)).toString();
            c5= Math.floor((Math.random()*100)).toString();
            c6= Math.floor((Math.random()*100)).toString();
                        
            if (value['local']==undefined )
              alert(padre+' '+value['local']+', '+index +' '+value['foreign']+' local undefined' );
            if (value['foreign']==undefined){
              //alert('tablas['+index+'] uno '+uno);
              alert(padre+' '+value['local']+', '+index +' '+value['foreign']+' foreing undefined' );  
            }
            //relaciones.push( { uno: uno, dos: dos, color: "#"+c1+c2+c3 } "#"+c1+c2+c3);
            if (uno!= undefined && dos!=undefined)
              relaciones.push( paper.connection(dos , uno, "rgb("+c1+","+c2+","+c3+")" , "rgb("+c4+","+c5+","+c6+")|3" ) );
          
          });
      });
  });
  
  $.each(tablas , function(index,value){
       value.toFront();
       value.setOpacity(.9);
  });

}
</script>
    <style type="text/css" media="screen">
        body {
          background-color: #ddd;
        }
        #holder {
            border: dotted 1px #333;
            width: 1000px;
            height: 1300px;
            margin-left: auto;
            margin-right: auto;
        }
        #c {
            border: solid 1px #333;
            width: 1000px;
            height: 100%;
            margin-left: auto;
            margin-right: auto;
            background-color: #fff;

        }
        p, h1 {
            text-align: center;
        }
    </style>
    </head>
    <body >
        <div id="c">
        <h1>Database Schema</h1>
        <div id="holder">
          
        </div>
        </div>
        <div id="datos"></div>

    </body>
</html>

