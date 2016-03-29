var fs = require('fs');
var system = require('system');
var args = system.args;
var defaultPageSize = {
    format: 'A4',
    orientation: 'portrait',
    margin: '1cm',
    header: {
        content: '',
        height: '1cm'
    },
    footer: {
        content: '',
        height: '1cm'
    }
};

if (args.length < 3) {

    console.log('Usage: phantomjs phantom-pdf.js $pageOptionJson');
    console.log('$pageOptionJson can contains following json-attributes: ' + JSON.stringify(defaultPageSize));

    phantom.exit();

} else {

    phantom.onError = getErrorHandler();

    var page = require('webpage').create();
    var resourcePath = args[1];
    var targetPath = args[2];
    var pageOptions = JSON.parse(args[3]);
    var format = {format: 'pdf'};
    var dirName = targetPath.substring(0, targetPath.lastIndexOf('/'));
    var pageNumPlaceholder = '#pageNum';
    var totalPagesPlaceholder = '#numPages';

    if (!fs.isWritable(dirName)) {
        printErrorAndExit('Path ' + dirName + ' is not writable.', 1);
    } else {
        try {

            if (pageOptions.pageNumPlaceholder) {
                pageNumPlaceholder = pageOptions.pageNumPlaceholder;
            }

            if (pageOptions.totalPagesPlaceholder) {
                totalPagesPlaceholder = pageOptions.totalPagesPlaceholder;
            }

            page.paperSize = getPaperSize(pageOptions);
            page.customHeaders = pageOptions.customHeaders;
            page.zoomFactor = pageOptions.zoomFactor;

            page.open(resourcePath, function (status) {
                if (status !== 'success') {
                    printErrorAndExit('Unable to load file.', 1)
                } else {
                    page.render(targetPath, format);
                    phantom.exit(0);
                }
            });
        } catch (e) {
            printErrorAndExit(e.toString(), 1);
        }
    }
}

function getPaperSize(pageOptions) {
    var paperSize = defaultPageSize;

    if (pageOptions.headerContent || pageOptions.headerHeight) {
        var headerHeight = pageOptions.headerHeight || defaultPageSize.header.height;
        var headerContent = pageOptions.headerContent;
        prepareContent(headerContent);
        paperSize.header = renderTemplate(headerHeight, headerContent);
    }

    if (pageOptions.footerContent || pageOptions.footerHeight) {
        var footerHeight = pageOptions.footerHeight || defaultPageSize.footer.height;
        var footerContent = pageOptions.footerContent;
        prepareContent(footerContent);
        paperSize.footer = renderTemplate(footerHeight, footerContent);
    }

    if (pageOptions.format) {
        paperSize.format = pageOptions.format;
    }

    if (pageOptions.orientation) {
        paperSize.orientation = pageOptions.orientation;
    }

    if (pageOptions.margin) {
        paperSize.margin = pageOptions.margin;
    }

    return paperSize;
}

function renderTemplate(height, content) {
    return {
        height: height,
        contents: phantom.callback(function (pageNum, numPages) {
            return content
                .replace(pageNumPlaceholder, pageNum)
                .replace(totalPagesPlaceholder, numPages)
                ;
        })
    };
}

function getErrorHandler() {
    return function (msg, trace) {

        var msgStack = ['ERROR: ' + msg];

        if (trace && trace.length) {
            msgStack.push('TRACE:');
            trace.forEach(function (t) {
                msgStack.push(' -> ' + t.file + ': ' + t.line + (t.function ? ' (in function "' + t.function + '")' : ''));
            });
        }

        printErrorAndExit(msgStack.join('\n'), 1);
    };
}

function printErrorAndExit(message, exitCode) {
    system.stderr.write(message);
    phantom.exit(exitCode);
}

function prepareContent(content) {
    var contentDom = document.createElement('html');
    contentDom.innerHTML = content;
}

