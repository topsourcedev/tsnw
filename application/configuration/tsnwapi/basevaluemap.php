<?php

$resources = array(
    "tools" => array(
        "comments" => array(
            "modcomments" => array(
                "vtiger_module" => "ModComments"
            )
        ),
        "users" => array(
            "vtusers" => array(
                "vtiger_module" => "Users"
            )
        )
    ),
    "crm" => array(
        "cd" => array(
            "accounts" => array(
                "vtiger_module" => "Accounts"
            ),
            "contacts" => array(
                "vtiger_module" => "Contacts",
            )
        ),
        "rm" => array(
            "events" => array(
                "vtiger_module" => "Events"
            ),
            "calendars" => array(
                "vtiger_module" => "Calendar"
            )
        )
    ),
    "bpm" => array(
        "eco" => array(
            "invoices" => array(
                "vtiger_module" => "Invoice"
            ),
            "collections" => array(
                "custom_description" => array(
                    "name" => "collections",
                    "tables" => "external_collections a",
                    "createable" => TRUE,
                    "updateable" => TRUE,
                    "deleteable" => TRUE,
                    "retrieveable" => TRUE,
                    "database" => "vtiger",
                    "fields" => array(
                        "a.id" => array("mandatory" => TRUE, "editable" => FALSE),
                        "a.amount" => array("mandatory" => TRUE),
                        "a.createdate" => array(),
                        "a.editdate" => array(),
                        "a.type" => array(),
                        "a.description" => array(),
                        "a.accountid" => array("type" => "reference", "refersTo" => "crm.cd.accounts"),
                        "a.state" => array(),
                        "a.ref" => array(),
                        "a.bankid" => array(),
                        "a.emissiondate" => array(),
                        "a.receiptdate" => array(),
                        "a.depositdate" => array(),
                        "a.valuedate" => array(),
                        "a.ourbankid" => array()
                    ),
                    "deleted" => array("a.deleted", "0", "1")
                )
            ),
            "banks" => array(
                "custom_description" => array(
                    "name" => "banks",
                    "tables" => "external_banks a",
                    "createable" => TRUE,
                    "updateable" => TRUE,
                    "deleteable" => TRUE,
                    "retrieveable" => TRUE,
                    "database" => "vtiger",
                    "fields" => array(
                        "a.bankid" => array("mandatory" => TRUE, "editable" => FALSE),
                        "a.bankabi" => array("mandatory" => TRUE, "editable" => FALSE),
                        "a.bankcab" => array("mandatory" => TRUE, "editable" => FALSE),
                        "a.bankname" => array(),
                        "a.bankdescription" => array(),
                        "a.bankstreet" => array(),
                        "a.bankcode" => array(),
                        "a.bankcity" => array(),
                        "a.bankprovince" => array()
                    ),
                    "deleted" => "deleted"
                )
            ),
            "project_collections" => array(
                "custom_description" => array(
                    "name" => "pjtcollections",
                    "tables" => "external_project_collections a",
                    "createable" => TRUE,
                    "updateable" => TRUE,
                    "deleteable" => TRUE,
                    "retrieveable" => TRUE,
                    "database" => "vtiger",
                    "fields" => array(
                        "a.id" => array("mandatory" => TRUE, "editable" => FALSE),
                        "a.salesorderid" => array("type" => "reference", "refersTo" => "bpm.com.salesorder"),
                        "a.projectid" => array("mandatory" => TRUE, "editable" => FALSE, "type" => "reference", "refersTo" => "bpm.pjm.projects"),
                        "a.date" => array(),
                        "a.status" => array("mandatory" => TRUE),
                        "a.flowstatus" => array("mandatory" => TRUE),
                        "a.amount" => array("mandatory" => TRUE),
                        "a.create_date" => array(),
                        "a.type" => array(),
                        "a.isactual" => array(),
                        "a.actualdate" => array(),
                        "a.actualamount" => array(),
                        "a.actualtype" => array()
                    ),
                    "deleted" => "deleted"
                )
            ),
            "project_invoices" => array(
                "custom_description" => array(
                    "name" => "pjtinvoices",
                    "tables" => "external_project_invoices a",
                    "createable" => TRUE,
                    "updateable" => TRUE,
                    "deleteable" => TRUE,
                    "retrieveable" => TRUE,
                    "database" => "vtiger",
                    "fields" => array(
                        "a.id" => array("mandatory" => TRUE, "editable" => FALSE),
                        "a.salesorderid" => array("type" => "reference", "refersTo" => "bpm.com.salesorder"),
                        "a.projectid" => array("mandatory" => TRUE, "editable" => FALSE, "type" => "reference", "refersTo" => "bpm.pjm.projects"),
                        "a.date" => array(),
                        "a.status" => array("mandatory" => TRUE),
                        "a.flowstatus" => array("mandatory" => TRUE),
                        "a.amount" => array("mandatory" => TRUE),
                        "a.create_date" => array(),
                        "a.isactual" => array(),
                        "a.actualdate" => array(),
                        "a.actualamount" => array()
                    ),
                    "deleted" => "deleted"
                )
            )//,
//            "project_salesorders",
//            "analysis_salesorders",
//            "project_quotes",
//            "analysis_quotes",
//            "project_potentials",
//            "analysis_potentials"
        ),
        "com" => array(
            "potentials" => array(
                "vtiger_module" => "Potentials"
            ),
            "quotes" => array(
                "vtiger_module" => "Quotes"
            ),
            "salesorders" => array(
                "vtiger_module" => "SalesOrder"
            ),
            "services" => array(
                "vtiger_module" => "Services"
            )
        ),
        "pjm" => array(
            "projects" => array(
                "vtiger_module" => "Project"
            ),
            "project_milestones" => array(
                "vtiger_module" => "ProjectMilestone"
            ),
            "temporary_milestones" => array(
                "custom_description" => array(
                    "name" => "tmpmilestones",
                    "tables" => "external_project_milestone a",
                    "createable" => TRUE,
                    "updateable" => TRUE,
                    "deleteable" => TRUE,
                    "retrieveable" => TRUE,
                    "database" => "vtiger",
                    "fields" => array(
                        "a.milestoneid" => array("mandatory" => TRUE, "editable" => FALSE),
                        "a.label" => array(),
                        "a.projectid" => array("mandatory" => TRUE, "editable" => FALSE, "type" => "reference", "refersTo" => "bpm.pjm.projects"),
                        "a.date" => array(),
                        "a.status" => array("mandatory" => TRUE),
                        "a.flowstatus" => array("mandatory" => TRUE),
                        "a.create_date" => array(),
                        "a.isactual" => array(),
                        "a.actualdate" => array(),
                        "a.actuallabel" => array()
                    ),
                    "deleted" => "deleted"
                )
            ),
            "project_tasks" => array(
                "vtiger_module" => "ProjectTask"
            )
        )
    )
);
?>