/*Credits: Dynamic Drive CSS Library */
/*URL: http://www.dynamicdrive.com/style/ */

a.ovalbutton{
    background: transparent url('../mod/predictions/images/oval-blue-left.gif') no-repeat top left;
    display: block;
    float: left;
    font: normal 13px Tahoma; /* Change 13px as desired */
    line-height: 16px; /* This value + 4px + 4px (top and bottom padding of SPAN) must equal height of button background (default is 24px) */
    height: 24px; /* Height of button background height */
    padding-left: 11px; /* Width of left menu image */
    text-decoration: none;
}

a:link.ovalbutton, a:visited.ovalbutton, a:active.ovalbutton{
    color: #494949; /*button text color*/
}

a.ovalbutton span{
    background: transparent url('../mod/predictions/images/oval-blue-right.gif') no-repeat top right;
    display: block;
    padding: 4px 11px 4px 0; /*Set 11px below to match value of 'padding-left' value above*/
}

a.ovalbutton:hover{ /* Hover state CSS */
    background-position: bottom left;
}

a.ovalbutton:hover span{ /* Hover state CSS */
    background-position: bottom right;
color: black;
}

.buttonwrapper{ /* Container you can use to surround a CSS button to clear float */
    overflow: hidden; /*See: http://www.quirksmode.org/css/clearing.html */
    width: 100%;
}
 
 /* grid definitions */

.col_1_of_12 {
    width : 8%;
}

.col_1_of_12_last {
    width : 12%;
}

.col_2_of_12_last {
    width : 20%;
}

.col_2_of_12 {
    width : 16%;
}

.col_3_of_12 {
    width : 24%;
}

.predictions_table {
        border-collapse:collapse;
        width:100%;
}
.predictions_table td {
        text-align:center;
        font-size:12px;
        font-family:"Lucida Sans Unicode","Lucida Grande",Sans-Serif;
        padding: 0px 0px 5px;
}

.predictions_table th {
        color:#003399;
        font-family:"Lucida Sans Unicode","Lucida Grande",Sans-Serif;
        font-size:14px;
        font-weight:bold;
        padding: 4px 0px 10px 0px;
        text-align:center;
}

.predictions_table .odd {
        background:none repeat scroll 0 0 #E8EDFF;
}

/* Table sorter */

table.tablesorter thead tr .header {
    background-image: url("../mod/predictions/images/bg.gif");
    background-repeat: no-repeat;
    background-position: 50% 95%;
    cursor: pointer;
}

table.tablesorter thead tr .headerSortUp {
    background-image: url("../mod/predictions/images/asc.gif");
}

table.tablesorter thead tr .headerSortDown {
    background-image: url("../mod/predictions/images/desc.gif");
}

<?php include dirname(dirname(dirname(dirname(__FILE__)))).'/css/anytimec.css' ?>

