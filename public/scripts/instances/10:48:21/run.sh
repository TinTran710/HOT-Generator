#!/bin/bash  
. demo-openrc
openstack stack create -t base.yml -e parameter.yml MyStack_670
