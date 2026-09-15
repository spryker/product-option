/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

require('./main');
var tableAccess = require('ZedGuiModules/libs/table/table-access');

var TABLE_SELECTORS = 'table.gui-table-data[id],table.gui-table-data-no-search[id]';

var allProductsTable;
var productOptionTable;

function removeActionHandler() {
    var $link = $(this);
    var tableHandler = $link.data('action') === 'select' ? allProductsTable : productOptionTable;

    if (tableHandler) {
        tableHandler.removeSelectedProduct($link.data('id'));
    }

    return false;
}

/**
 * The panels of this page are shown one at a time, so a table is measured while it is still hidden and
 * has to be measured again once its panel is opened - which is also the moment it gets created.
 *
 * @param {Object} container - Element holding the tables.
 */
function adjustTables(container) {
    container.find(TABLE_SELECTORS).each(function (index, table) {
        tableAccess.requestTable(table, function (handle) {
            handle.refreshLayout();
        });
    });
}

function ProductSelector() {
    var productSelector = {};
    var selectedProducts = {};

    /**
     * @param {number} idProduct - ID of the product.
     * @param {Array} row - Row the product is shown with in the table of the selection.
     */
    productSelector.addProductToSelection = function (idProduct, row) {
        selectedProducts[idProduct] = row;
    };

    productSelector.removeProductFromSelection = function (idProduct) {
        delete selectedProducts[idProduct];
    };

    productSelector.isProductSelected = function (idProduct) {
        return selectedProducts.hasOwnProperty(idProduct);
    };

    productSelector.clearAllSelections = function () {
        selectedProducts = {};
    };

    productSelector.getSelected = function () {
        return selectedProducts;
    };

    /**
     * @returns {Array} Rows of every selected product, the table of the selection is built from them.
     */
    productSelector.getRows = function () {
        return Object.keys(selectedProducts).map(function (idProduct) {
            return selectedProducts[idProduct];
        });
    };

    return productSelector;
}

function TableHandler(sourceTable, destinationTable, checkBoxNamePrefix, labelCaption, labelId, action, formFieldId) {
    var tableHandler = {
        checkBoxNamePrefix: checkBoxNamePrefix,
        labelId: labelId,
        labelCaption: labelCaption,
        action: action,
        formFieldId: formFieldId,
        sourceTable: sourceTable,
        destinationTable: destinationTable,
    };

    var destinationTableProductSelector = new ProductSelector();
    var sourceHandle = null;
    var destinationHandle = null;

    tableHandler.selectAll = function () {
        if (!sourceHandle) {
            return;
        }

        var api = sourceHandle.raw();

        $('input[type="checkbox"]', api.rows().nodes().toArray()).prop('checked', true);

        api.rows()
            .data()
            .each(function (cellData) {
                tableHandler.addSelectedProduct(cellData[0], cellData[1], cellData[2]);
            });
    };

    tableHandler.deSelectAll = function () {
        if (!sourceHandle) {
            return;
        }

        var api = sourceHandle.raw();

        $('input[type="checkbox"]', api.rows().nodes().toArray()).prop('checked', false);

        api.rows()
            .data()
            .each(function (cellData) {
                tableHandler.removeSelectedProduct(cellData[0]);
            });
    };

    tableHandler.addSelectedProduct = function (idProduct, sku, name) {
        if (destinationTableProductSelector.isProductSelected(idProduct)) {
            return;
        }

        destinationTableProductSelector.addProductToSelection(
            idProduct,
            tableHandler.buildSelectionRow(idProduct, sku, name),
        );

        tableHandler.renderSelection();
        tableHandler.updateSelectedProductsLabelCount();
    };

    tableHandler.removeSelectedProduct = function (idProduct) {
        if (destinationTableProductSelector.isProductSelected(idProduct)) {
            destinationTableProductSelector.removeProductFromSelection(idProduct);
            tableHandler.renderSelection();
            $('#' + tableHandler.getCheckBoxNamePrefix() + idProduct).prop('checked', false);
        }

        tableHandler.updateSelectedProductsLabelCount();
    };

    /**
     * @param {number} idProduct - ID of the product.
     * @param {string} sku - SKU of the product.
     * @param {string} name - Name of the product.
     *
     * @returns {Array} Row of the table of the selection, its last cell holding the button which undoes it.
     */
    tableHandler.buildSelectionRow = function (idProduct, sku, name) {
        return [
            idProduct,
            decodeURIComponent((sku + '').replace(/\+/g, '%20')),
            decodeURIComponent((name + '').replace(/\+/g, '%20')),
            '<div><a data-id="' +
                idProduct +
                '" data-action="' +
                tableHandler.getAction() +
                '" href="#" class="btn btn-xs remove-item">Remove</a></div>',
        ];
    };

    /**
     * The table of the selection is a view over it and is rebuilt from it, so that it can be filled
     * whenever the plugin gets round to creating it: every panel of this page but the first is closed
     * while the page loads, so those tables are created only once their panel is opened.
     */
    tableHandler.renderSelection = function () {
        if (!destinationHandle) {
            return;
        }

        destinationHandle.raw().clear().rows.add(destinationTableProductSelector.getRows()).draw(false);
    };

    tableHandler.getSelector = function () {
        return destinationTableProductSelector;
    };

    tableHandler.updateSelectedProductsLabelCount = function () {
        $('#' + tableHandler.getLabelId()).text(
            labelCaption + ' (' + Object.keys(this.getSelector().getSelected()).length + ')',
        );
        var productIds = Object.keys(this.getSelector().getSelected());
        var s = productIds.join(',');
        var field = $('#' + tableHandler.getFormFieldId());
        field.attr('value', s);
    };

    tableHandler.getCheckBoxNamePrefix = function () {
        return tableHandler.checkBoxNamePrefix;
    };

    tableHandler.getLabelId = function () {
        return tableHandler.labelId;
    };

    tableHandler.getAction = function () {
        return tableHandler.action;
    };

    tableHandler.getLabelCaption = function () {
        return tableHandler.labelCaption;
    };

    tableHandler.getFormFieldId = function () {
        return tableHandler.formFieldId;
    };

    tableHandler.getSourceTable = function () {
        return tableHandler.sourceTable;
    };

    tableHandler.getDestinationTable = function () {
        return tableHandler.destinationTable;
    };

    /**
     * @returns {Object|null} Handle of the source table, absent while the plugin is still to create it.
     */
    tableHandler.getSourceHandle = function () {
        return sourceHandle;
    };

    tableAccess.requestTable(sourceTable[0], function (handle) {
        sourceHandle = handle;
    });

    tableAccess.requestTable(destinationTable[0], function (handle) {
        destinationHandle = handle;

        handle.created().then(function () {
            tableHandler.renderSelection();
        });
    });

    return tableHandler;
}

$(document).ready(function () {
    var currentTableId = 'products-to-assign';
    var $allProducts = $('#product-table');
    var $productOptions = $('#product-option-table');

    if (!$allProducts.length) {
        return;
    }

    allProductsTable = new TableHandler(
        $allProducts,
        $('#selectedProductsTable'),
        'all_products_checkbox_',
        'Products to be assigned',
        'products-to-be-assigned',
        'select',
        'product_option_general_products_to_be_assigned',
    );

    $('#selectedProductsTable, #deselectedProductsTable').on('click', '.remove-item', removeActionHandler);

    $allProducts.on('change', '.all-products-checkbox', function () {
        var $checkbox = $(this);
        var info = $.parseJSON($checkbox.attr('data-info'));

        if ($checkbox.prop('checked')) {
            allProductsTable.addSelectedProduct(info.id, info.sku, info.name);

            return;
        }

        allProductsTable.removeSelectedProduct(info.id);
    });

    tableAccess.requestTable($allProducts[0], function (handle) {
        handle.on('draw', function () {
            var selector = allProductsTable.getSelector();

            handle
                .raw()
                .rows()
                .data()
                .each(function (cellData) {
                    var idProduct = parseInt(cellData[0]);

                    if (selector.isProductSelected(idProduct)) {
                        $('#' + allProductsTable.getCheckBoxNamePrefix() + idProduct).prop('checked', true);
                    }
                });
        });
    });

    $('#product-selectors').on('click', '.btn', function () {
        var $button = $(this);
        var dataElementId = $button.attr('id');

        $('#product-selectors .btn').removeClass('active');
        $button.addClass('active');

        $('#products-assignment > div').hide();

        currentTableId = dataElementId;

        if (dataElementId == 'products-to-assign' || dataElementId === 'assigned') {
            $('#select-all-btn').show();
        } else {
            $('#select-all-btn').hide();
        }

        var productContainer = $('#products-assignment').find('[data-products="' + dataElementId + '"]');

        productContainer.show();
        adjustTables(productContainer);
    });

    $('#select-all').on('click', function () {
        if (currentTableId == 'products-to-assign' || !productOptionTable) {
            allProductsTable.selectAll();
        } else {
            productOptionTable.selectAll();
        }
        return false;
    });

    $('#deselect-all').on('click', function () {
        if (currentTableId == 'products-to-assign' || !productOptionTable) {
            allProductsTable.deSelectAll();
        } else {
            productOptionTable.deSelectAll();
        }
        return false;
    });

    if (!$productOptions.length) {
        return;
    }

    productOptionTable = new TableHandler(
        $productOptions,
        $('#deselectedProductsTable'),
        'product_category_checkbox_',
        'Products to be deassigned',
        'to-be-deassigned',
        'deselect',
        'product_option_general_products_to_be_de_assigned',
    );

    /**
     * Deassignment is the mirror image of assignment: every assigned product starts out checked, so
     * clearing a checkbox stages a removal rather than undoing a selection, and the select-all and
     * deselect-all buttons swap roles with it.
     */
    productOptionTable.deSelectAll = function () {
        var sourceHandle = productOptionTable.getSourceHandle();

        if (!sourceHandle) {
            return;
        }

        var api = sourceHandle.raw();

        $('input[type="checkbox"]', api.rows().nodes().toArray()).prop('checked', false);

        api.rows()
            .data()
            .each(function (cellData) {
                productOptionTable.addSelectedProduct(cellData[0], cellData[1], cellData[2]);
            });
    };

    productOptionTable.selectAll = function () {
        var sourceHandle = productOptionTable.getSourceHandle();

        if (!sourceHandle) {
            return;
        }

        var api = sourceHandle.raw();

        $('input[type="checkbox"]', api.rows().nodes().toArray()).prop('checked', true);

        api.rows()
            .data()
            .each(function (cellData) {
                productOptionTable.removeSelectedProduct(cellData[0]);
            });
    };

    productOptionTable.removeSelectedProduct = function (idProduct) {
        var selector = productOptionTable.getSelector();

        if (selector.isProductSelected(idProduct)) {
            selector.removeProductFromSelection(idProduct);
            productOptionTable.renderSelection();
        }

        productOptionTable.updateSelectedProductsLabelCount();
    };

    $productOptions.on('change', '.product_category_checkbox', function () {
        var $checkbox = $(this);
        var info = $.parseJSON($checkbox.attr('data-info'));

        if ($checkbox.prop('checked')) {
            productOptionTable.removeSelectedProduct(info.id);
            allProductsTable.removeSelectedProduct(info.id);

            return;
        }

        productOptionTable.addSelectedProduct(info.id, info.sku, info.name);
    });

    tableAccess.requestTable($productOptions[0], function (handle) {
        handle.on('draw', function () {
            var selector = productOptionTable.getSelector();

            handle
                .raw()
                .rows()
                .data()
                .each(function (cellData) {
                    var idProduct = parseInt(cellData[0]);

                    if (selector.isProductSelected(idProduct)) {
                        $('#' + productOptionTable.getCheckBoxNamePrefix() + idProduct).prop('checked', false);
                    }
                });
        });
    });
});
