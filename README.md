# Introduction 
Simple SQL profiler output for Magento CLI commands

##Install
To install `git clone` the module to `app/code/Shaun/Profiler`

#Uninstall
1. Run `bin/magento module:disable Shaun_Profiler`
2. Run `rm -rf app/code/Shaun/Profiler`
3. Run `bin/magento setup:upgrade`
4. Run `bin/magento setup:di:compile`

##Getting Started
1. run `bin/magento module:enable Shaun_Profiler`
2. Edit `app/code/Shaun/Profiler/etc/di.xml` add commands you want to profile.

*example:*
```
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <type name="{MyComandToProfile}">
        <plugin name="Shaun_CliProfiler" type="Shaun\Profiler\Plugin\CliProfilerPlugin" sortOrder="1" disabled="false" />
    </type>
</config>
```

3. Make sure the commands `excute` method is `public`
4. Run `bin/magento setup:upgrade`
5. Run `bin/magento setup:di:compile`

##Example output
```
+-------------+-----------------------------------------------+--------------+
| Time 0.61ms | SQL[Total:5]                                  | Query params |
+-------------+-----------------------------------------------+--------------+
| 0.32ms      | connect                                       | []           |
| 0.06ms      | SET NAMES utf8                                | []           |
| 0.11ms      | SELECT `store_website`.* FROM `store_website` | []           |
| 0.06ms      | SELECT `store_group`.* FROM `store_group`     | []           |
| 0.05ms      | SELECT `store`.* FROM `store`                 | []           |
+-------------+-----------------------------------------------+--------------+
```
