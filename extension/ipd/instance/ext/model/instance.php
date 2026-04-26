<?php
public function isClickable($instance, $action)
{
    return $this->loadExtension('instance')->isClickable($instance, $action);
}

public function installationSettingsMap($customData, $dbInfo, $instance)
{
    return $this->loadExtension('instance')->installationSettingsMap($customData, $dbInfo, $instance);
}

public function checkAccessForWS($instance)
{
    return $this->loadExtension('instance')->checkAccessForWS($instance);
}

public function getZenTaoApp()
{
    return $this->loadExtension('instance')->getZenTaoApp();
}

public function stop($instance)
{
    return $this->loadExtension('instance')->stop($instance);
}

public function deleteZenTaoApp()
{
    return $this->loadExtension('instance')->deleteZenTaoApp();
}

public function getOptionsByApi($query)
{
    return $this->loadExtension('instance')->getOptionsByApi($query);
}
