
/*TODO 
  *make the validate / invalid  visible
  *check on database whether  the username or email already exist

*/

new function() {

       // $.fn.validate = validate() {};

    $.fn.validate = {

        init: function(o) {
	  

          if(o.name == 'data[User][username]') { this.username(o) };


          if(o.name == 'data[User][password]') { this.password(o) };

          if(o.name == 'data[User][email]') { this.email(o) };


        },

        username: function(o) {

           var user = /[A-Za-z_]{2,20}/;
           if (o.value.match(user)) {

             doValidate(o);

            } else {
	
             doError(o);
             // __('username should be longer than 2 characters, and you can only use letters and \'_\'')
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
      alert ('good :-)');
};



function doError(o) {

      // display error text
      alert ('wrong :-(');
};

     
// this function check the data in the database
function doValidate(o) {


  //TODO add a 

  doSuccess(o);

};






$(document).ready(function()
{

  $(".validated").blur(function() {
          $(this).validate.init(this);
  });
  
});
