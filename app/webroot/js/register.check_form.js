
/*TODO 
  *make the validate / invalid  visible
  *check on database whether  the username or email already exist

*/

new function() {

       // $.fn.validate = validate() {};

    $.fn.validate = {

        init: function(o) {
	  

          if(o.name == 'data[User][username]') { this.username(o) };

/*
          if(o.name == 'data[User][password]') { this.password(o) };

          if(o.name == 'data[User][email]') { this.email(o) };
*/

        },

        username: function(o) {

           var user = /[A-Za-z_]{2,20}/;
           if (o.value.match(user)) {

             doValidate(o);

            } else {
	
             doError(o);
             // __()
            };

        },

        email: function(o) {

	  
          var email  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

           if (o.value.match(email)) {
              doValidate(o);
            } else {

              doError(o);
              // ,__('not a valid email')
	    };

        },



        password: function(o) {

	  
          var password  = /(.){4,}/;

           if (o.value.match(password)) {
              doSuccess(o);
            } else {

              doError(o);
              // ,__('password should at least be 4 characters long')	   
            };
           


        }


  };



};



function doSuccess(o) {
      // add something visible ( a "ok" picture ?)
      //alert ('good :-)');
       $('#' + o.id +'_error').html('');
};



function doError(o) {

      // display error text
      //alert ('wrong :-(');

          if(o.name == 'data[User][username]') { var error_text = 'Username can only contain letters, numbers, or underscore' };

/*
          if(o.name == 'data[User][password]') { var error_text = 'Password must be at least two letters' };

          if(o.name == 'data[User][email]') { var error_text = 'Non valid email' };
*/

          $('#'+ o.id +'_error').html(error_text);

};

     
// this function check the data in the database
function doValidate(o) {
  
  if(o.name == 'data[User][username]') {

     $('#' + o.id +'_error').load("http://" + self.location.hostname + "/users/check_username/" + o.value) 

  } ;
  /*if(o.name == 'data[User][email]') { $('#' + o.id +'_error').load("http://" + self.location.hostname + "/users/check_email/" + o.value) } ;*/
  // add load() ajax function  The URL would be : "http://" + self.location.hostname + "/users/check_username/" + username
  //TODO add a 

  doSuccess(o);

};






$(document).ready(function()
{

  $(".validated").blur(function() {
          $(this).validate.init(this);
  });
  
});
