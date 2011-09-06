<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 * @since      0.9.12
 */

/**
 * This node class represents a <b>include</b>- or <b>include_once</b>-expression.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: 0.10.6
 * @link       http://www.pdepend.org/
 * @since      0.9.12
 */
class PHP_Depend_Code_ASTIncludeExpression extends PHP_Depend_Code_ASTExpression
{
    /**
     * The type of this class.
     */
    const CLAZZ = __CLASS__;

    /**
     * Does this node represent a <b>include_once</b>-expression?
     *
     * @var boolean
     */
    protected $once = false;

    /**
     * Does this node represent a <b>include_once</b>-expression?
     *
     * @return boolean
     */
    public function isOnce()
    {
        return $this->once;
    }

    /**
     * Flags this node as a <b>include_once</b>-expression.
     *
     * @return void
     */
    public function setOnce()
    {
        $this->once = true;
    }

    /**
     * Accept method of the visitor design pattern. This method will be called
     * by a visitor during tree traversal.
     *
     * @param PHP_Depend_Code_ASTVisitorI $visitor The calling visitor instance.
     * @param mixed                       $data    Optional previous calculated data.
     *
     * @return mixed
     */
    public function accept(PHP_Depend_Code_ASTVisitorI $visitor, $data = null)
    {
        return $visitor->visitIncludeExpression($this, $data);
    }

    /**
     * The magic sleep method will be called by PHP's runtime environment right
     * before an instance of this class gets serialized. It should return an
     * array with those property names that should be serialized for this class.
     *
     * @return array(string)
     * @since 0.10.0
     */
    public function  __sleep()
    {
        return array_merge(array('once'), parent::__sleep());
    }
}