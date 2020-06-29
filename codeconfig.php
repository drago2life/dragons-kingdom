<?

  // Background color in RGB format
  $bgRed		= 80;
  $bgGreen		= 50;
  $bgBlue		= 89;

  /***************************************************************/

  // Render Grid
  // (Displays a grid over the image for added security)
  // (1 = on, 0 = off)
  $renderGrid		= 1;

  // Grid Square Size
  // Size in pixels, make sure that both $x_size and $y_size is divisible by this value.
  $squareSize		= 10;

  // Grid border color in RGB format
  $gridRed		= 164;
  $gridGreen		= 164;
  $gridBlue		= 164;

  /***************************************************************/

  // Text color in RGB format
  $txtRed		= 255;
  $txtGreen		= 255;
  $txtBlue		= 255;

  // Image size in pixels
  $x_size		= 140;
  $y_size		= 30;

  // Starting text alignment: These values must be changed if the Image Size is changed.
  // These values might require a little experimentation to get correct.
  $txtHorizontal	= 21;
  // The below value is the value used to space out 
  $txtPixelSpace	= 21;

  // The below values need to be differnt so we can get a random number from 5 to 10 which
  // is how the text is staggered.
  $txtVertRangeStart	= 5;
  $txtVertRangeStop	= 10;

  // Generated code length.
  // If you change this default value you will have to change the image length as well so it fits into view.
  $codeLength		= 5;

  /********************************
  *        Security Options       *
  ********************************/
  // Enable URL refferal protection.
  // (This makes sure that only allowed domains can use your script.)
  // To enable make sure the below value is "true", to disable make sure its "false";
  $secEnabled		= "false";

  // Sites allowed to call the script.
  // Please seperate sites with |
  // Example("www.ebay.com|www.yahoo.com|www.google.com|");
  // (Note: If you are having trouble getting it to allow access to a trusted site then try using a IP address instead.)
  $secSiteList		= "www.dk-rpg.com";
  
  /*
	Note:	DO NOT ADD A ?> TO END THIS PHP FILE!
		It will cause the script to fail.
  */
	