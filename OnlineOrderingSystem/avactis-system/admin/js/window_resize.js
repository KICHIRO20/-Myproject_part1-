function WindowResize(width, height)
{
    var windowWidth = width;
    var windowHeight = height;
    var screenWidth = screen.availWidth;
    var screenHeight = screen.availHeight;

    window.resizeTo(windowWidth, windowHeight);
    window.moveTo((screenWidth-windowWidth)/2, (screenHeight-windowHeight)/2);
}

function WindowMaximize()
{
    var screenWidth = screen.availWidth;
    var screenHeight = screen.availHeight;

    window.resizeTo(screenWidth, screenHeight);
    window.moveTo(0, 0);
}