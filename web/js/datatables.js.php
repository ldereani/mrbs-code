<?php

// $Id$

require "../defaultincludes.inc";

header("Content-type: application/x-javascript");
expires_header(60*30); // 30 minute expiry

if ($use_strict)
{
  echo "'use strict';\n";
}


// Get the types, which are assumed to be in a data-type in a <span> in the <th>
// of the table
?>
var getTypes = function getTypes(table) {
    var type,
        types = {},
        result = [];
        
    table.find('thead tr:first th').each(function(i) {
       var type = $(this).find('span').data('type');
       if (type)
       {
         if (types[type] === undefined)
         {
           types[type] = [];
         }
         types[type].push(i);
       }
      });

    for (type in types)
    {
      if (types.hasOwnProperty(type))
      {
        result.push({type: type, 
                     targets: types[type]});
      }
    }

    return result;
  };
  
        
<?php
// Turn the table with id 'id' into a DataTable, using specificOptions
// which are merged with the default options.   If the browser is IE6 or less
// we don't bother making a dataTable:  it can be done, but it's not worth it.
//
// fixedColumnsOptions is an optional object that gets passed directly to the
// DataTables FixedColumns constructor
//
// If you want to do anything else as part of fnInitComplete then you'll need
// to define fnInitComplete in specificOptions
?>
        
function makeDataTable(id, specificOptions, fixedColumnsOptions)
{
  var winWidth  = $(window).width(),
      winHeight = $(window).height(),
      i,
      defaultOptions, mergedOptions,
      nCols,
      table;
  
  if (lteIE6)
  {
    $('.js div.datatable_container').css('visibility', 'visible');
    return false;
  }
  else
  {
    table = $(id);
    if (table.length === 0)
    {
      return false;
    }
    <?php
    // Remove the <colgroup>.  This is only needed to assist in the formatting
    // of the non-datatable version of the table.   When we have a datatable,
    // the datatable sorts out its own formatting.
    ?>
    table.find('colgroup').remove();
    <?php // Set up the default options ?>
    defaultOptions = {};
    <?php
    // Set the language file to be used
    if ($lang_file = get_datatable_lang_file('../jquery/datatables/language'))
    {
      // If using the language.url way of loading a DataTables language file,
      // then the file must be valid JSON.   The .lang files that can be 
      // downloaded from GitHub are not valid JSON as they contain comments.  They
      // therefore cannot be used with language.url, but instead have to be
      // included directly.   Note that if ever we go back to using the url
      // method then the '../' would need to be stripped off the pathname, as in
      //    $lang_file = substr($lang_file, 3); // strip off the '../'
      ?>
      defaultOptions.oLanguage = <?php include $lang_file ?>;
      <?php
    }
    ?>
    defaultOptions.deferRender = true;
    defaultOptions.paging = true;
    defaultOptions.pageLength = 25;
    defaultOptions.pagingType = "full_numbers";
    defaultOptions.processing = true;
    defaultOptions.scrollCollapse = true;
    defaultOptions.stateSave = true;
    defaultOptions.pageLength = 25;
    defaultOptions.dom = 'C<"clear">lfrtip';
    defaultOptions.scrollX = "100%";
    defaultOptions.colReorder = {};
    defaultOptions.colVis = {buttonText: '<?php echo escape_js(get_vocab("show_hide_columns")) ?>',
                             restore: '<?php echo escape_js(get_vocab("restore_original")) ?>'};
              
    <?php
    // If we've fixed the left or right hand columns, then (a) remove them
    // from the column visibility list because they are fixed and (b) stop them
    // from being reordered
    ?>
    var colVisExcludeCols = [];
    if (fixedColumnsOptions)
    {
      if (fixedColumnsOptions.leftColumns)
      { 
        for (i=0; i<fixedColumnsOptions.leftColumns; i++)
        {
          colVisExcludeCols.push(i);
        }
        defaultOptions.colReorder.fixedColumnsLeft = fixedColumnsOptions.leftColumns;
      }
      if (fixedColumnsOptions.rightColumns)
      { 
        nCols = table.find('tr:first-child th').length;
        for (i=0; i<fixedColumnsOptions.rightColumns; i++)
        {
          colVisExcludeCols.push(nCols - (i+1));
        }
        defaultOptions.colReorder.fixedColumnsRight = fixedColumnsOptions.rightColumns;
      }
    }
    defaultOptions.colVis.exclude = colVisExcludeCols;
    <?php
    // Merge the specific options with the default options.  We do a deep
    // merge.
    ?>
    mergedOptions = $.extend(true, {}, defaultOptions, specificOptions);

    var datatable = table.DataTable(mergedOptions);
    
    if (fixedColumnsOptions)
    {
      new $.fn.dataTable.FixedColumns(datatable, fixedColumnsOptions);
    }

    <?php
    // If we're using an Ajax data source then don't offer column reordering.
    // This is a problem at the moment in DataTables because if you reorder a column
    // DataTables doesn't know that the Ajax data is still in the original order.
    // May be fixed in a future release of DataTables
    ?>
    if (!specificOptions.ajax)
    {
      <?php
      /*
      // In fact we don't use column reordering at all, because (a) it doesn't
      // work with an Ajax source (b) there's no way of fixing the right hand column
      // (c) iFixedColumns doesn't seem to work properly and (d) it's confusing
      // for the user having reordering enabled sometimes and sometimes not.  Better
      // to wait for a future release of DataTables when these issues have been
      // fixed.  In the meantime the line of code we need is there below so we can see
      // how it is done, but commented out.
              
      var oCR = new ColReorder(oTable, mergedOptions);
              
      */
      ?>
    }

    $('.js div.datatable_container').css('visibility', 'visible');
    <?php // Need to adjust column sizing after the table is made visible ?>
    datatable.columns.adjust();
    
    <?php
    // Adjust the column sizing on a window resize.   We shouldn't have to do this because
    // columns.adjust() is called automatically by DataTables on a window resize, but if we
    // don't then a right hand fixed column appears twice when a window's width is increased.
    // I have tried to create a simple test case, but everything works OK in the test case, so
    // it's something to do with the way MRBS uses DataTables - maybe the CSS, or maybe the
    // JavaScript.
    ?>
    $(window).resize(function () {
      datatable.columns.adjust();
    });
    
    return datatable;
  }
}