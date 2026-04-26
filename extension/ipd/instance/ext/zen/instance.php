<?php
protected function storeView($id)
{
    return $this->loadExtension('instance')->storeView($id);
}

protected function buildCustomConfig($appID)
{
    return $this->loadExtension('instance')->buildCustomConfig($appID);
}

protected function checkCustomFields($appID)
{
    return $this->loadExtension('instance')->checkCustomFields($appID);
}
