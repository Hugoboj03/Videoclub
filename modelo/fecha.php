<?PHP

// -----------------------------------------------------------------
// Biblioteca de variables y funciones para el manejo de fechas
// -----------------------------------------------------------------



// -----------------------------------------------------------------
// Convertir fecha a cadena
// -----------------------------------------------------------------
function date2string($date){
   // Formato: 'j' día del mes (número, sin ceros) /
   //          'n' mes del año (número, sin ceros) /
   //          'Y' año (cuatro dígitos)
   // Ejemplo: 7/11/2002
   //strtotime($date): Funcion que convierte una cadena de fecha ($date) en un valor de tiempo (timestamp)
   $string = date("j/n/Y", strtotime($date));
   return ($string);
}
