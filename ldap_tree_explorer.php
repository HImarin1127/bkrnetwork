<?php
// LDAP 樹狀結構完整探索工具
session_start();

$ldapConfig = require __DIR__ . '/config/ldap.php';

function connectLDAP($ldapConfig) {
    $server = $ldapConfig['use_ssl'] 
        ? "ldaps://{$ldapConfig['server']}:{$ldapConfig['port']}"
        : "ldap://{$ldapConfig['server']}:{$ldapConfig['port']}";
        
    $connection = ldap_connect($server);
    
    if (!$connection) {
        return false;
    }
    
    ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
    ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, $ldapConfig['timeout']);
    
    if ($ldapConfig['use_tls']) {
        if (!ldap_start_tls($connection)) {
            ldap_unbind($connection);
            return false;
        }
    }
    
    if (!ldap_bind($connection, $ldapConfig['admin_username'], $ldapConfig['admin_password'])) {
        ldap_unbind($connection);
        return false;
    }
    
    return $connection;
}

function exploreLDAPTree($connection, $baseDN, $filter = '(objectClass=*)', $scope = 'sub') {
    $attributes = ['dn', 'objectClass', 'uid', 'cn', 'sAMAccountName', 'userPrincipalName', 
                   'mail', 'description', 'memberOf', 'member', 'department', 'title',
                   'telephoneNumber', 'createTimestamp', 'modifyTimestamp', 'userAccountControl'];
    
    try {
        if ($scope === 'one') {
            $search = ldap_list($connection, $baseDN, $filter, $attributes);
        } else {
            $search = ldap_search($connection, $baseDN, $filter, $attributes);
        }
        
        if (!$search) {
            return ['success' => false, 'message' => 'LDAP搜尋失敗: ' . ldap_error($connection)];
        }
        
        $entries = ldap_get_entries($connection, $search);
        $results = [];
        
        for ($i = 0; $i < $entries['count']; $i++) {
            $entry = $entries[$i];
            $item = [
                'dn' => $entry['dn'],
                'objectClass' => isset($entry['objectclass']) ? $entry['objectclass'] : [],
                'uid' => isset($entry['uid'][0]) ? $entry['uid'][0] : '',
                'cn' => isset($entry['cn'][0]) ? $entry['cn'][0] : '',
                'sAMAccountName' => isset($entry['samaccountname'][0]) ? $entry['samaccountname'][0] : '',
                'userPrincipalName' => isset($entry['userprincipalname'][0]) ? $entry['userprincipalname'][0] : '',
                'mail' => isset($entry['mail'][0]) ? $entry['mail'][0] : '',
                'description' => isset($entry['description'][0]) ? $entry['description'][0] : '',
                'department' => isset($entry['department'][0]) ? $entry['department'][0] : '',
                'title' => isset($entry['title'][0]) ? $entry['title'][0] : '',
                'phone' => isset($entry['telephonenumber'][0]) ? $entry['telephonenumber'][0] : '',
                'created' => isset($entry['createtimestamp'][0]) ? $entry['createtimestamp'][0] : '',
                'modified' => isset($entry['modifytimestamp'][0]) ? $entry['modifytimestamp'][0] : '',
                'userAccountControl' => isset($entry['useraccountcontrol'][0]) ? $entry['useraccountcontrol'][0] : '',
                'memberOf' => isset($entry['memberof']) ? $entry['memberof'] : [],
                'member' => isset($entry['member']) ? $entry['member'] : []
            ];
            $results[] = $item;
        }
        
        return ['success' => true, 'results' => $results, 'count' => $entries['count']];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => '錯誤: ' . $e->getMessage()];
    }
}

// 建立LDAP連接
$connection = connectLDAP($ldapConfig);
$connectionStatus = $connection ? '✅ 連接成功' : '❌ 連接失敗';

$explorations = [];

if ($connection) {
    // 多種探索策略
    $strategies = [
        [
            'name' => '🎯 直接搜尋 ldapuser',
            'base' => $ldapConfig['base_dn'],
            'filter' => '(|(uid=ldapuser)(cn=ldapuser)(sAMAccountName=ldapuser))',
            'description' => '使用多種屬性直接搜尋 ldapuser'
        ],
        [
            'name' => '📋 瀏覽整個 cn=users 組織單位',
            'base' => 'cn=users,dc=bookrep,dc=com,dc=tw',
            'filter' => '(objectClass=*)',
            'description' => '列出 cn=users 中的所有條目'
        ],
        [
            'name' => '🌐 瀏覽整個域的頂層結構',
            'base' => $ldapConfig['base_dn'],
            'filter' => '(objectClass=*)',
            'scope' => 'one',
            'description' => '查看域的直接子級結構'
        ],
        [
            'name' => '👥 搜尋所有 inetOrgPerson',
            'base' => $ldapConfig['base_dn'],
            'filter' => '(objectClass=inetOrgPerson)',
            'description' => '標準用戶對象類型'
        ],
        [
            'name' => '👤 搜尋所有 person',
            'base' => $ldapConfig['base_dn'],
            'filter' => '(objectClass=person)',
            'description' => '基本人員對象類型'
        ],
        [
            'name' => '🔧 搜尋所有 organizationalPerson',
            'base' => $ldapConfig['base_dn'],
            'filter' => '(objectClass=organizationalPerson)',
            'description' => '組織人員對象類型'
        ],
        [
            'name' => '⚙️ 搜尋系統和服務帳號',
            'base' => $ldapConfig['base_dn'],
            'filter' => '(|(objectClass=account)(objectClass=simpleSecurityObject)(description=*service*)(description=*system*))',
            'description' => '專門搜尋服務和系統帳號'
        ],
        [
            'name' => '🔍 搜尋所有包含 ldap 的條目',
            'base' => $ldapConfig['base_dn'],
            'filter' => '(|(uid=*ldap*)(cn=*ldap*)(description=*ldap*))',
            'description' => '包含 ldap 關鍵字的所有條目'
        ]
    ];
    
    foreach ($strategies as $strategy) {
        $scope = isset($strategy['scope']) ? $strategy['scope'] : 'sub';
        $result = exploreLDAPTree($connection, $strategy['base'], $strategy['filter'], $scope);
        $explorations[] = [
            'strategy' => $strategy,
            'result' => $result
        ];
    }
    
    ldap_unbind($connection);
}

function formatObjectClasses($objectClasses) {
    if (!is_array($objectClasses) || !isset($objectClasses['count'])) {
        return '';
    }
    
    $classes = [];
    for ($i = 0; $i < $objectClasses['count']; $i++) {
        $classes[] = $objectClasses[$i];
    }
    return implode(', ', $classes);
}

function isServiceAccount($item) {
    $description = strtolower($item['description']);
    $cn = strtolower($item['cn']);
    $uid = strtolower($item['uid']);
    
    $serviceKeywords = ['service', 'system', 'admin', 'ldap', 'bind', 'proxy'];
    
    foreach ($serviceKeywords as $keyword) {
        if (strpos($description, $keyword) !== false || 
            strpos($cn, $keyword) !== false || 
            strpos($uid, $keyword) !== false) {
            return true;
        }
    }
    
    return false;
}

function highlightLdapUser($text) {
    return str_ireplace('ldapuser', '<span class="highlight">ldapuser</span>', htmlspecialchars($text));
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDAP 樹狀結構完整探索工具</title>
    <style>
        body {
            font-family: 'Microsoft JhengHei', sans-serif;
            margin: 20px;
            background: #f5f5f5;
            line-height: 1.6;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        .warning { background: #fff3cd; border-color: #ffeaa7; color: #856404; }
        .service-account { background: #e7f3ff; border-color: #b6d7ff; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 0.85rem;
        }
        th, td {
            padding: 6px 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
            word-wrap: break-word;
            max-width: 200px;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
            position: sticky;
            top: 0;
        }
        tr:hover { background: #f8f9fa; }
        
        .dn-text {
            font-family: monospace;
            font-size: 0.75rem;
            color: #666;
            word-break: break-all;
        }
        
        .highlight {
            background: yellow;
            font-weight: bold;
            padding: 2px 4px;
            border-radius: 3px;
        }
        
        .service-indicator {
            background: #17a2b8;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.7rem;
            margin-left: 5px;
        }
        
        .object-classes {
            font-size: 0.75rem;
            color: #666;
            max-width: 150px;
            word-break: break-word;
        }
        
        .collapsible {
            cursor: pointer;
            user-select: none;
        }
        
        .collapsible:before {
            content: "▼ ";
        }
        
        .collapsed:before {
            content: "▶ ";
        }
        
        .content {
            display: block;
        }
        
        .content.hidden {
            display: none;
        }
        
        .summary-box {
            background: #e8f4f8;
            border: 1px solid #bee5eb;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌳 LDAP 樹狀結構完整探索工具</h1>
        <p>深度探索 LDAP 結構，包含服務帳號和系統帳號</p>
        
        <div class="section info">
            <h3>🔌 連接狀態</h3>
            <p><strong>LDAP 伺服器：</strong> <?= $ldapConfig['server'] ?>:<?= $ldapConfig['port'] ?></p>
            <p><strong>連接狀態：</strong> <?= $connectionStatus ?></p>
            <p><strong>服務帳號：</strong> <?= $ldapConfig['admin_username'] ?></p>
        </div>

        <?php if ($connection): ?>
            <div class="summary-box">
                <h3>📊 探索摘要</h3>
                <?php 
                $totalItems = 0;
                $foundLdapUser = false;
                $serviceAccounts = 0;
                
                foreach ($explorations as $exploration) {
                    if ($exploration['result']['success']) {
                        $totalItems += $exploration['result']['count'];
                        foreach ($exploration['result']['results'] as $item) {
                            if (stripos($item['uid'], 'ldapuser') !== false || 
                                stripos($item['cn'], 'ldapuser') !== false) {
                                $foundLdapUser = true;
                            }
                            if (isServiceAccount($item)) {
                                $serviceAccounts++;
                            }
                        }
                    }
                }
                ?>
                <p><strong>總共探索項目：</strong> <?= $totalItems ?></p>
                <p><strong>發現 ldapuser：</strong> <?= $foundLdapUser ? '✅ 是' : '❌ 否' ?></p>
                <p><strong>疑似服務帳號：</strong> <?= $serviceAccounts ?></p>
            </div>

            <?php foreach ($explorations as $exploration): ?>
                <div class="section <?= $exploration['result']['success'] ? 'success' : 'error' ?>">
                    <h3 class="collapsible" onclick="toggleContent(this)"><?= htmlspecialchars($exploration['strategy']['name']) ?></h3>
                    <div class="content">
                        <p><strong>搜尋基礎：</strong> <code><?= htmlspecialchars($exploration['strategy']['base']) ?></code></p>
                        <p><strong>過濾器：</strong> <code><?= htmlspecialchars($exploration['strategy']['filter']) ?></code></p>
                        <p><strong>說明：</strong> <?= htmlspecialchars($exploration['strategy']['description']) ?></p>
                        
                        <?php if ($exploration['result']['success']): ?>
                            <p><strong>結果：</strong> ✅ 找到 <?= $exploration['result']['count'] ?> 個項目</p>
                            
                            <?php if ($exploration['result']['count'] > 0): ?>
                                <div style="overflow-x: auto;">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>UID</th>
                                                <th>CN</th>
                                                <th>郵件</th>
                                                <th>描述</th>
                                                <th>對象類別</th>
                                                <th>DN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($exploration['result']['results'] as $item): ?>
                                                <?php 
                                                $isService = isServiceAccount($item);
                                                $hasLdapUser = (stripos($item['uid'], 'ldapuser') !== false || 
                                                              stripos($item['cn'], 'ldapuser') !== false);
                                                $rowClass = $hasLdapUser ? 'style="background: #fff3cd; font-weight: bold;"' : 
                                                           ($isService ? 'style="background: #e7f3ff;"' : '');
                                                ?>
                                                <tr <?= $rowClass ?>>
                                                    <td>
                                                        <?= highlightLdapUser($item['uid']) ?>
                                                        <?= $isService ? '<span class="service-indicator">服務</span>' : '' ?>
                                                    </td>
                                                    <td><?= highlightLdapUser($item['cn']) ?></td>
                                                    <td><?= htmlspecialchars($item['mail']) ?></td>
                                                    <td><?= highlightLdapUser($item['description']) ?></td>
                                                    <td class="object-classes"><?= formatObjectClasses($item['objectClass']) ?></td>
                                                    <td class="dn-text"><?= highlightLdapUser($item['dn']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p>🚫 沒有找到符合條件的項目</p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p><strong>錯誤：</strong> ❌ <?= htmlspecialchars($exploration['result']['message']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="section error">
                <h3>❌ 無法連接到 LDAP 伺服器</h3>
                <p>請檢查 LDAP 配置或網路連接</p>
            </div>
        <?php endif; ?>

        <div class="section warning">
            <h3>🔍 分析結果</h3>
            <h4>如果沒有找到 ldapuser：</h4>
            <ul>
                <li><strong>可能性1：</strong> ldapuser 確實不存在於 LDAP 中</li>
                <li><strong>可能性2：</strong> ldapuser 被隱藏或有特殊的查看權限</li>
                <li><strong>可能性3：</strong> ldapuser 在配置中只是範例，實際不存在</li>
                <li><strong>可能性4：</strong> ldapuser 在其他我們沒有權限查看的OU中</li>
            </ul>
            
            <h4>建議的解決方案：</h4>
            <ul>
                <li>使用上面找到的其他真實帳號進行 LDAP 登入測試</li>
                <li>聯繫 LDAP 管理員確認 ldapuser 的實際狀態</li>
                <li>檢查是否有其他服務帳號可以用於配置</li>
                <li>確認當前服務帳號的查看權限範圍</li>
            </ul>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="find_ldapuser.php"><button style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px;">🎯 專門搜尋工具</button></a>
            <a href="ldap_debug.php"><button style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px;">🧪 認證測試</button></a>
            <a href="login"><button style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; margin: 5px;">🔐 登入頁面</button></a>
        </div>
    </div>

    <script>
        function toggleContent(element) {
            const content = element.nextElementSibling;
            const isHidden = content.classList.contains('hidden');
            
            if (isHidden) {
                content.classList.remove('hidden');
                element.classList.remove('collapsed');
            } else {
                content.classList.add('hidden');
                element.classList.add('collapsed');
            }
        }
    </script>
</body>
</html> 