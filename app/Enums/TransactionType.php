<?php

namespace App\Enums;

enum TransactionType: string
{
    case DEPOSIT = 'deposit';        // Rechargement via TMoney/Flooz
    case WITHDRAWAL = 'withdrawal';  // Retrait vers mobile money (Vendeur)
    case PAYMENT = 'payment';        // Paiement d'une commande
    case COMMISSION = 'commission';  // Commission plateforme prélevée
    case REFUND = 'refund';          // Remboursement client
    case PROMO_REWARD = 'promo_reward'; // Gain parrainage/promo
    case TRANSFER = 'transfer';      // Transfert entre comptes (admin only)
}
