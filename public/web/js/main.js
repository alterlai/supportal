$(document).ready(function() {
   $('.dataTable').DataTable( {
      "searching": false
   });

   $('.addToCart').click(function () {
      alert("Document toegevoegd aan winkelmand");
   })
});